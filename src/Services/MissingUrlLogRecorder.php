<?php

namespace MightyWarnersKochi\SitemapKit\Services;

use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use MightyWarnersKochi\SitemapKit\Models\MissingUrlLog;

class MissingUrlLogRecorder
{
    /**
     * Persist a missing-URL hit when the response qualifies (HTTP 404 and/or soft 404).
     *
     * @param  \Symfony\Component\HttpFoundation\Response|null  $response
     */
    public function record(Request $request, $response): void
    {
        if (! config('sitemap_automation.not_found_logging.enabled', true)) {
            return;
        }

        if (! in_array($request->method(), config('sitemap_automation.not_found_logging.http_methods', ['GET', 'HEAD']), true)) {
            return;
        }

        $path = RedirectPathNormalizer::pathFromRequest($request);

        if ($this->shouldSkipPath($path)) {
            return;
        }

        if ($this->shouldSkipExtension($path)) {
            return;
        }

        if (! $this->shouldLogAsMissingUrl($response)) {
            return;
        }

        $url = $path;
        $referer = $request->headers->get('referer');
        $hash = hash('sha256', $url);

        $useHash = Schema::hasColumn('missing_url_logs', 'url_hash');

        if ($useHash) {
            $this->recordHitByHash($hash, $url, $referer);

            return;
        }

        /** @var MissingUrlLog|null $existing */
        $existing = MissingUrlLog::query()->where('url', $url)->first();

        if ($existing) {
            $this->touchExisting($existing, $referer);

            return;
        }

        try {
            MissingUrlLog::query()->create([
                'url' => $url,
                'referer' => $referer,
                'hit_count' => 1,
                'first_seen_at' => now(),
                'last_seen_at' => now(),
            ]);
        } catch (QueryException $e) {
            $this->recoverFromDuplicateUrl($url, $referer, $e);
        }
    }

    /**
     * @param  \Symfony\Component\HttpFoundation\Response|null  $response
     */
    protected function shouldLogAsMissingUrl($response): bool
    {
        if (! $response) {
            return false;
        }

        if ($response->getStatusCode() === 404) {
            return true;
        }

        $soft = config('sitemap_automation.not_found_logging.soft_404', []);

        if (! ($soft['enabled'] ?? false)) {
            return false;
        }

        $statuses = $soft['http_statuses'] ?? [200];
        if (! in_array($response->getStatusCode(), $statuses, true)) {
            return false;
        }

        return $this->responseBodyMatchesSoft404Markers($response, $soft);
    }

    /**
     * @param  \Symfony\Component\HttpFoundation\Response  $response
     */
    protected function responseBodyMatchesSoft404Markers($response, array $soft): bool
    {
        $contentType = strtolower((string) $response->headers->get('Content-Type', ''));
        if ($contentType === '' || strpos($contentType, 'text/html') === false) {
            return false;
        }

        $markers = $soft['body_markers'] ?? [];
        if (! is_array($markers) || $markers === []) {
            return false;
        }

        $content = $response->getContent();
        if (! is_string($content) || $content === '') {
            return false;
        }

        $max = (int) ($soft['max_bytes_to_scan'] ?? 65536);
        if ($max < 1) {
            $max = 65536;
        }

        $snippet = strlen($content) > $max ? substr($content, 0, $max) : $content;
        $lowerSnippet = Str::lower($snippet);

        foreach ($markers as $marker) {
            $marker = (string) $marker;
            if ($marker === '') {
                continue;
            }
            if (Str::contains($lowerSnippet, Str::lower($marker))) {
                return true;
            }
        }

        return false;
    }

    protected function recordHitByHash(string $hash, string $url, ?string $referer): void
    {
        /** @var MissingUrlLog|null $existing */
        $existing = MissingUrlLog::query()->where('url_hash', $hash)->first();

        if ($existing) {
            $this->touchExisting($existing, $referer);

            return;
        }

        try {
            MissingUrlLog::query()->create([
                'url_hash' => $hash,
                'url' => $url,
                'referer' => $referer,
                'hit_count' => 1,
                'first_seen_at' => now(),
                'last_seen_at' => now(),
            ]);
        } catch (QueryException $e) {
            $retry = MissingUrlLog::query()->where('url_hash', $hash)->first();
            if ($retry) {
                $this->touchExisting($retry, $referer);

                return;
            }

            if ($this->isDuplicateKeyException($e)) {
                return;
            }

            throw $e;
        }
    }

    protected function touchExisting(MissingUrlLog $existing, ?string $referer): void
    {
        $existing->increment('hit_count');
        $existing->forceFill([
            'referer' => $referer ?: $existing->referer,
            'last_seen_at' => now(),
        ])->save();
    }

    protected function recoverFromDuplicateUrl(string $url, ?string $referer, QueryException $e): void
    {
        if (! $this->isDuplicateKeyException($e)) {
            throw $e;
        }

        $existing = MissingUrlLog::query()->where('url', $url)->first();
        if ($existing) {
            $this->touchExisting($existing, $referer);
        }
    }

    protected function isDuplicateKeyException(QueryException $e): bool
    {
        $code = (string) $e->getCode();
        $sqlState = $e->errorInfo[0] ?? '';

        return $code === '23000' || $sqlState === '23000' || str_contains(strtolower($e->getMessage()), 'duplicate');
    }

    protected function shouldSkipPath(string $path): bool
    {
        $prefixes = config('sitemap_automation.not_found_logging.except_path_prefixes', ['/admin']);

        foreach ($prefixes as $prefix) {
            $p = RedirectPathNormalizer::normalizePath((string) $prefix);
            if ($p !== '/' && (strpos($path, $p) === 0)) {
                return true;
            }
        }

        return false;
    }

    protected function shouldSkipExtension(string $path): bool
    {
        foreach (config('sitemap_automation.not_found_logging.skip_extensions', []) as $ext) {
            $ext = ltrim((string) $ext, '.');
            if ($ext !== '' && Str::endsWith(strtolower($path), strtolower('.'.$ext))) {
                return true;
            }
        }

        return false;
    }
}
