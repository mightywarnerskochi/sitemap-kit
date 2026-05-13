<?php

namespace MightyWarnersKochi\SitemapKit\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use MightyWarnersKochi\SitemapKit\Services\RedirectPathNormalizer;
use MightyWarnersKochi\SitemapKit\Services\UrlRedirectService;

class ResolveUrlRedirects
{
    /** @var UrlRedirectService */
    protected $redirects;

    public function __construct(UrlRedirectService $redirects)
    {
        $this->redirects = $redirects;
    }

    /**
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (! config('sitemap_automation.redirects.enabled', true)) {
            return $next($request);
        }

        if (! in_array($request->method(), config('sitemap_automation.redirects.http_methods', ['GET', 'HEAD']), true)) {
            return $next($request);
        }

        $path = RedirectPathNormalizer::pathFromRequest($request);

        if ($this->shouldSkipPath($path)) {
            return $next($request);
        }

        $row = $this->redirects->findForPath($path);
        if ($row) {
            $response = $this->redirects->toResponse($row, $request);
            if (! $response) {
                return $next($request);
            }
            $this->redirects->recordHit($row);

            return $response;
        }

        $configResponse = $this->redirects->responseFromPathRedirects($request, $path);
        if ($configResponse) {
            return $configResponse;
        }

        return $next($request);
    }

    protected function shouldSkipPath(string $path): bool
    {
        $prefixes = config('sitemap_automation.redirects.except_path_prefixes', ['/admin']);

        foreach ($prefixes as $prefix) {
            $p = RedirectPathNormalizer::normalizePath((string) $prefix);
            if ($p !== '/' && (strpos($path, $p) === 0)) {
                return true;
            }
        }

        $lower = strtolower($path);
        foreach (config('sitemap_automation.redirects.skip_path_suffixes', []) as $suffix) {
            $suffix = (string) $suffix;
            if ($suffix !== '' && substr($lower, -strlen($suffix)) === strtolower($suffix)) {
                return true;
            }
        }

        return false;
    }
}
