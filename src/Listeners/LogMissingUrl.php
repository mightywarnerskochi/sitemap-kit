<?php

namespace MightyWarnersKochi\SitemapKit\Listeners;

use Illuminate\Foundation\Http\Events\RequestHandled;
use Illuminate\Support\Str;
use MightyWarnersKochi\SitemapKit\Models\MissingUrlLog;
use MightyWarnersKochi\SitemapKit\Services\RedirectPathNormalizer;

class LogMissingUrl
{
    /**
     * Handle the event.
     *
     * @return void
     */
    public function handle(RequestHandled $event)
    {
        if (! config('sitemap_automation.not_found_logging.enabled', true)) {
            return;
        }

        $response = $event->response;
        if (! $response || $response->getStatusCode() !== 404) {
            return;
        }

        $request = $event->request;

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

        $url = $path;
        $referer = $request->headers->get('referer');

        /** @var MissingUrlLog|null $existing */
        $existing = MissingUrlLog::query()->where('url', $url)->first();

        if ($existing) {
            $existing->increment('hit_count');
            $existing->forceFill([
                'referer' => $referer ?: $existing->referer,
                'last_seen_at' => now(),
            ])->save();

            return;
        }

        MissingUrlLog::query()->create([
            'url' => $url,
            'referer' => $referer,
            'hit_count' => 1,
            'first_seen_at' => now(),
            'last_seen_at' => now(),
        ]);
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
