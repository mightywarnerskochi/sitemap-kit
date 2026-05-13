<?php

namespace MightyWarnersKochi\SitemapKit\Observers;

use Illuminate\Database\Eloquent\Model;
use MightyWarnersKochi\SitemapKit\Services\ModelRedirectRecorder;
use MightyWarnersKochi\SitemapKit\Support\RedirectConfiguration;

class UrlRedirectObserver
{
    /** @var ModelRedirectRecorder */
    protected $recorder;

    public function __construct(ModelRedirectRecorder $recorder)
    {
        $this->recorder = $recorder;
    }

    public function updating(Model $model): void
    {
        if (! config('sitemap_automation.redirects.enabled', true)) {
            return;
        }

        $map = RedirectConfiguration::mergedModels();
        $class = get_class($model);
        if (! isset($map[$class])) {
            return;
        }

        $cfg = $map[$class];
        $slugField = isset($cfg['slug_field']) ? $cfg['slug_field'] : 'slug';
        $urlPrefix = isset($cfg['url_prefix']) ? $cfg['url_prefix'] : null;

        if (! $urlPrefix || ! $model->isDirty($slugField)) {
            return;
        }

        $old = (string) $model->getOriginal($slugField);
        $new = (string) $model->getAttribute($slugField);

        $this->recorder->recordSlugChange($model, (string) $urlPrefix, $slugField, $old, $new);
    }

    public function deleting(Model $model): void
    {
        if (! config('sitemap_automation.redirects.enabled', true)) {
            return;
        }

        $map = RedirectConfiguration::mergedModels();
        $class = get_class($model);
        if (! isset($map[$class])) {
            return;
        }

        $cfg = $map[$class];
        $slugField = isset($cfg['slug_field']) ? $cfg['slug_field'] : 'slug';
        $urlPrefix = isset($cfg['url_prefix']) ? $cfg['url_prefix'] : null;

        if (! $urlPrefix) {
            return;
        }

        $onDelete = (isset($cfg['on_delete']) && is_array($cfg['on_delete']))
            ? $cfg['on_delete']
            : ['strategy' => 'gone'];

        $this->recorder->recordDeletion($model, (string) $urlPrefix, $slugField, $onDelete);
    }
}
