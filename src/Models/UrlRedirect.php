<?php

namespace MightyWarnersKochi\SitemapKit\Models;

use Illuminate\Database\Eloquent\Model;

class UrlRedirect extends Model
{
    protected $table = 'url_redirects';

    protected static function booted()
    {
        static::saving(function (UrlRedirect $model) {
            $path = $model->old_url;
            if (is_string($path) && $path !== '') {
                $model->old_url_hash = hash('sha256', $path);
            }
        });
    }

    protected $fillable = [
        'old_url',
        'old_url_hash',
        'new_url',
        'status_code',
        'hit_count',
        'created_by',
        'source',
        'notes',
    ];

    protected $casts = [
        'status_code' => 'integer',
        'hit_count' => 'integer',
        'created_by' => 'integer',
    ];

    /**
     * Increment hit counter (atomic).
     *
     * @return void
     */
    public function recordHit(): void
    {
        $this->increment('hit_count');
    }
}
