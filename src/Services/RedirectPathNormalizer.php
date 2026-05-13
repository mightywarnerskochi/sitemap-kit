<?php

namespace MightyWarnersKochi\SitemapKit\Services;

use Illuminate\Http\Request;

class RedirectPathNormalizer
{
    /**
     * Normalize a request path to a canonical form used in the database.
     * Leading slash, no trailing slash (except root).
     */
    public static function normalizePath(string $path): string
    {
        $path = '/'.ltrim(trim($path), '/');
        if ($path !== '/' && substr($path, -1) === '/') {
            $path = rtrim($path, '/');
        }

        return $path;
    }

    /**
     * Path from Laravel request (path() is without leading slash).
     */
    public static function pathFromRequest(Request $request): string
    {
        return self::normalizePath('/'.$request->path());
    }

    public static function isAbsoluteUrl(string $url): bool
    {
        return (bool) preg_match('#^https?://#i', $url);
    }

    /**
     * Build a public path from prefix and slug segment.
     *
     * @param  string  $urlPrefix  e.g. "/blog/" or "blog/"
     */
    public static function joinPrefixAndSlug(string $urlPrefix, string $slug): string
    {
        $prefix = self::normalizePath($urlPrefix);
        $slug = trim($slug, '/');

        if ($slug === '') {
            return $prefix;
        }

        return self::normalizePath($prefix.'/'.$slug);
    }
}
