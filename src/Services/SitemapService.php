<?php

namespace Dev1kochiCrypto\SitemapKit\Services;

use Spatie\Sitemap\SitemapGenerator;

class SitemapService
{
    /**
     * Generate the sitemap and write it to the public directory.
     */
    public function generate(): void
    {
        \Spatie\Sitemap\SitemapGenerator::create(config('app.url'))
            ->hasCrawled(function (\Spatie\Sitemap\Tags\Url $url) {
                if ($url->segment(1) === null) {
                    $url->setPriority(1.0);
                } else {
                    $url->setPriority(0.8);
                }
                $url->setLastModificationDate(now());
                return $url;
            })
            ->writeToFile(public_path('sitemap.xml'));
    }

    /**
     * Check if the sitemap file exists.
     */
    public function exists(): bool
    {
        return file_exists(public_path('sitemap.xml'));
    }
}
