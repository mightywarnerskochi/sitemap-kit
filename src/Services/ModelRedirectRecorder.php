<?php

namespace MightyWarnersKochi\SitemapKit\Services;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class ModelRedirectRecorder
{
    /** @var UrlRedirectService */
    protected $redirects;

    public function __construct(UrlRedirectService $redirects)
    {
        $this->redirects = $redirects;
    }

    /**
     * Store a 301 from old slug URL to new slug URL.
     */
    public function recordSlugChange(Model $model, string $urlPrefix, string $slugField, string $oldSlug, string $newSlug): void
    {
        if ($oldSlug === '' || $newSlug === '' || $oldSlug === $newSlug) {
            return;
        }

        $oldPath = RedirectPathNormalizer::joinPrefixAndSlug($urlPrefix, $oldSlug);
        $newPath = RedirectPathNormalizer::joinPrefixAndSlug($urlPrefix, $newSlug);

        $this->redirects->upsertRule($oldPath, $newPath, 301, [
            'source' => 'auto_slug',
            'created_by' => Auth::id(),
            'notes' => 'Auto: slug change on '.get_class($model).' #'.$model->getKey(),
        ]);
    }

    /**
     * Apply configured delete strategy as a redirect or 410 row.
     *
     * @param  array<string, mixed>  $onDelete
     */
    public function recordDeletion(Model $model, string $urlPrefix, string $slugField, array $onDelete): void
    {
        $slug = (string) $model->getAttribute($slugField);
        if ($slug === '') {
            return;
        }

        $oldPath = RedirectPathNormalizer::joinPrefixAndSlug($urlPrefix, $slug);
        $strategy = isset($onDelete['strategy']) ? (string) $onDelete['strategy'] : 'gone';

        if ($strategy === 'gone') {
            $this->redirects->upsertRule($oldPath, null, 410, [
                'source' => 'auto_delete',
                'created_by' => Auth::id(),
                'notes' => 'Auto: deleted resource (410)',
            ]);

            return;
        }

        if ($strategy === 'listing') {
            $listing = isset($onDelete['listing_path']) ? (string) $onDelete['listing_path'] : '';
            if ($listing === '') {
                return;
            }
            $this->redirects->upsertRule($oldPath, $this->normalizeTargetUrl($listing), 301, [
                'source' => 'auto_delete',
                'created_by' => Auth::id(),
                'notes' => 'Auto: deleted resource → listing',
            ]);

            return;
        }

        if ($strategy === 'url') {
            $target = isset($onDelete['target_url']) ? (string) $onDelete['target_url'] : '';
            if ($target === '') {
                return;
            }
            $this->redirects->upsertRule($oldPath, $this->normalizeTargetUrl($target), 301, [
                'source' => 'auto_delete',
                'created_by' => Auth::id(),
                'notes' => 'Auto: deleted resource → custom URL',
            ]);

            return;
        }

        if ($strategy === 'model_method') {
            $method = isset($onDelete['method']) ? (string) $onDelete['method'] : '';
            if ($method === '' || ! method_exists($model, $method)) {
                return;
            }
            $result = $model->{$method}();
            $this->applyMethodResult($oldPath, $result);

            return;
        }

        if ($strategy === 'relation') {
            $relation = isset($onDelete['relation']) ? (string) $onDelete['relation'] : '';
            $relatedSlugField = isset($onDelete['relation_slug_field']) ? (string) $onDelete['relation_slug_field'] : 'slug';
            $pathPrefix = isset($onDelete['relation_path_prefix']) ? (string) $onDelete['relation_path_prefix'] : '/';

            if ($relation === '' || ! method_exists($model, $relation)) {
                return;
            }

            $related = $model->{$relation}()->first();
            if (! $related) {
                $this->fallbackWhenRelationMissing($oldPath, $onDelete);

                return;
            }

            $relatedSlug = (string) $related->getAttribute($relatedSlugField);
            if ($relatedSlug === '') {
                $this->fallbackWhenRelationMissing($oldPath, $onDelete);

                return;
            }

            $target = RedirectPathNormalizer::joinPrefixAndSlug($pathPrefix, $relatedSlug);
            $this->redirects->upsertRule($oldPath, $target, 301, [
                'source' => 'auto_delete',
                'created_by' => Auth::id(),
                'notes' => 'Auto: deleted resource → related',
            ]);
        }
    }

    /**
     * @param  mixed  $result  string path/url, array with keys target/status_code, or null to skip
     */
    protected function applyMethodResult(string $oldPath, $result): void
    {
        if ($result === null) {
            return;
        }

        if (is_string($result)) {
            if ($result === '') {
                return;
            }
            if (strtoupper($result) === 'GONE' || strtoupper($result) === '410') {
                $this->redirects->upsertRule($oldPath, null, 410, [
                    'source' => 'auto_delete',
                    'created_by' => Auth::id(),
                    'notes' => 'Auto: model_method → 410',
                ]);

                return;
            }
            $normalizedTarget = RedirectPathNormalizer::isAbsoluteUrl($result)
                ? $result
                : RedirectPathNormalizer::normalizePath($result);
            $this->redirects->upsertRule($oldPath, $normalizedTarget, 301, [
                'source' => 'auto_delete',
                'created_by' => Auth::id(),
                'notes' => 'Auto: model_method',
            ]);

            return;
        }

        if (is_array($result)) {
            $target = isset($result['target']) ? $result['target'] : null;
            $code = isset($result['status_code']) ? (int) $result['status_code'] : 301;

            if ($code === 410) {
                $this->redirects->upsertRule($oldPath, null, 410, [
                    'source' => 'auto_delete',
                    'created_by' => Auth::id(),
                    'notes' => 'Auto: model_method → 410',
                ]);

                return;
            }

            if (is_string($target) && $target !== '') {
                $normalizedTarget = RedirectPathNormalizer::isAbsoluteUrl($target)
                    ? $target
                    : RedirectPathNormalizer::normalizePath($target);
                $this->redirects->upsertRule($oldPath, $normalizedTarget, $code, [
                    'source' => 'auto_delete',
                    'created_by' => Auth::id(),
                    'notes' => 'Auto: model_method',
                ]);
            }
        }
    }

    /**
     * @param  array<string, mixed>  $onDelete
     */
    protected function fallbackWhenRelationMissing(string $oldPath, array $onDelete): void
    {
        $fallback = isset($onDelete['fallback_strategy']) ? (string) $onDelete['fallback_strategy'] : 'gone';

        if ($fallback === 'listing' && isset($onDelete['listing_path'])) {
            $this->redirects->upsertRule($oldPath, $this->normalizeTargetUrl((string) $onDelete['listing_path']), 301, [
                'source' => 'auto_delete',
                'created_by' => Auth::id(),
                'notes' => 'Auto: relation missing → listing',
            ]);

            return;
        }

        $this->redirects->upsertRule($oldPath, null, 410, [
            'source' => 'auto_delete',
            'created_by' => Auth::id(),
            'notes' => 'Auto: relation missing → 410',
        ]);
    }

    protected function normalizeTargetUrl(string $target): string
    {
        if (RedirectPathNormalizer::isAbsoluteUrl($target)) {
            return $target;
        }

        return RedirectPathNormalizer::normalizePath($target);
    }
}
