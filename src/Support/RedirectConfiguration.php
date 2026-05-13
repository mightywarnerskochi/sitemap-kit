<?php

namespace MightyWarnersKochi\SitemapKit\Support;

class RedirectConfiguration
{
    /**
     * Models that participate in automatic redirect recording.
     *
     * @return array<string, array<string, mixed>>
     */
    public static function mergedModels(): array
    {
        $merged = [];

        if (config('sitemap_automation.redirects.observe_sitemap_models', true)) {
            $merged = self::normalizeModelsConfig(config('sitemap_automation.models', []));
        }

        $extra = self::normalizeModelsConfig(config('sitemap_automation.redirects.models', []));

        foreach ($extra as $class => $cfg) {
            $merged[$class] = array_merge(isset($merged[$class]) ? $merged[$class] : [], $cfg);
        }

        return $merged;
    }

    /**
     * @param  array<int|string, mixed>  $modelsWithConfig
     * @return array<string, array<string, mixed>>
     */
    public static function normalizeModelsConfig(array $modelsWithConfig): array
    {
        $out = [];
        foreach ($modelsWithConfig as $key => $value) {
            $modelClass = is_numeric($key) ? $value : $key;
            if (! is_string($modelClass) || ! class_exists($modelClass)) {
                continue;
            }

            $cfg = is_array($value) ? $value : [];
            $out[$modelClass] = $cfg;
        }

        return $out;
    }
}
