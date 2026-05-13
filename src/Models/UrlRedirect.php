<?php

namespace MightyWarnersKochi\SitemapKit\Models;

use Illuminate\Database\Eloquent\Model;

class UrlRedirect extends Model
{
    protected $table = 'url_redirects';

    protected $fillable = [
        'old_url',
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
