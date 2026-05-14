<?php

namespace MightyWarnersKochi\SitemapKit\Services;

use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use MightyWarnersKochi\SitemapKit\Models\UrlRedirect;

class UrlRedirectService
{
    /** @var UrlRedirect|null */
    protected $matched;

    /**
     * Find redirect for the given normalized path.
     */
    public function findForPath(string $normalizedPath): ?UrlRedirect
    {
        $hash = hash('sha256', $normalizedPath);
        $this->matched = UrlRedirect::query()->where('old_url_hash', $hash)->first();

        return $this->matched;
    }

    /**
     * Build a response from config path_redirects (static pages, campaigns, etc.).
     * No hit_count; use DB rules if you need analytics for the same path.
     *
     * @return \Symfony\Component\HttpFoundation\Response|null
     */
    public function responseFromPathRedirects(Request $request, string $normalizedPath)
    {
        $map = config('sitemap_automation.redirects.path_redirects', []);
        if (! is_array($map) || $map === []) {
            return null;
        }

        foreach ($map as $old => $spec) {
            $key = RedirectPathNormalizer::normalizePath((string) $old);
            if ($key !== $normalizedPath) {
                continue;
            }

            return $this->responseFromPathRedirectSpec($spec, $request);
        }

        return null;
    }

    /**
     * @param  mixed  $spec
     * @return \Symfony\Component\HttpFoundation\Response|null
     */
    protected function responseFromPathRedirectSpec($spec, Request $request)
    {
        if (is_string($spec)) {
            $target = trim($spec);
            if ($target === '') {
                return null;
            }

            return redirect()->to($this->absoluteUrl($target, $request), 301);
        }

        if (! is_array($spec)) {
            return null;
        }

        $target = null;
        $code = 301;

        if (array_key_exists('to', $spec) || array_key_exists('target', $spec)) {
            $t = $spec['to'] ?? $spec['target'] ?? null;
            $target = is_string($t) ? trim($t) : null;
            $code = isset($spec['status']) ? (int) $spec['status'] : (isset($spec['status_code']) ? (int) $spec['status_code'] : 301);
        } elseif (array_key_exists(0, $spec)) {
            $t = $spec[0];
            $target = is_string($t) ? trim($t) : null;
            $code = isset($spec[1]) ? (int) $spec[1] : 301;
        }

        if ($code === 410) {
            return response('', 410);
        }

        if ($target === null || $target === '') {
            return null;
        }

        $redirectStatus = ($code >= 300 && $code < 400) ? $code : 302;

        return redirect()->to($this->absoluteUrl($target, $request), $redirectStatus);
    }

    /**
     * Build a response for a redirect row, or null if not applicable.
     *
     * @return \Symfony\Component\HttpFoundation\Response|null
     */
    public function toResponse(UrlRedirect $row, Request $request)
    {
        $code = (int) $row->status_code;

        if ($code === 410) {
            return response('', 410);
        }

        if ($row->new_url === null || $row->new_url === '') {
            return null;
        }

        $target = $this->absoluteUrl($row->new_url, $request);

        /** @var Redirector $redirect */
        $redirect = redirect();
        $redirectStatus = ($code >= 300 && $code < 400) ? $code : 302;

        return $redirect->to($target, $redirectStatus);
    }

    /**
     * Turn stored new_url into an absolute URL when needed.
     */
    public function absoluteUrl(string $newUrl, Request $request): string
    {
        if (preg_match('#^https?://#i', $newUrl)) {
            return $newUrl;
        }

        return $request->getSchemeAndHttpHost().RedirectPathNormalizer::normalizePath($newUrl);
    }

    /**
     * Record a hit for the last matched row.
     */
    public function recordHit(?UrlRedirect $row = null): void
    {
        $row = $row ?: $this->matched;
        if ($row) {
            $row->recordHit();
        }
    }

    /**
     * Create or update a redirect rule.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function upsertRule(string $oldUrl, ?string $newUrl, int $statusCode, array $attributes = []): UrlRedirect
    {
        $oldUrl = RedirectPathNormalizer::normalizePath($oldUrl);
        if ($newUrl !== null && $newUrl !== '' && ! RedirectPathNormalizer::isAbsoluteUrl($newUrl)) {
            $newUrl = RedirectPathNormalizer::normalizePath($newUrl);
        }

        $payload = array_merge([
            'old_url' => $oldUrl,
            'new_url' => $newUrl,
            'status_code' => $statusCode,
        ], $attributes);

        /** @var UrlRedirect $model */
        $model = UrlRedirect::query()->updateOrCreate(
            ['old_url_hash' => hash('sha256', $oldUrl)],
            $payload
        );

        return $model;
    }
}
