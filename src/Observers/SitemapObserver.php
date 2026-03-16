<?php

namespace Dev1kochiCrypto\SitemapKit\Observers;

use Dev1kochiCrypto\SitemapKit\Jobs\UpdateSitemapJob;

class SitemapObserver
{
    /**
     * Handle the model "created" event.
     */
    public function created($model): void
    {
        dispatch(new UpdateSitemapJob($model));
    }

    /**
     * Handle the model "updated" event.
     */
    public function updated($model): void
    {
        dispatch(new UpdateSitemapJob($model));
    }

    /**
     * Handle the model "deleted" event.
     */
    public function deleted($model): void
    {
        dispatch(new UpdateSitemapJob($model, true));
    }
}
