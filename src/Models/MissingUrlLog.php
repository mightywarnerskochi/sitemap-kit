<?php

namespace MightyWarnersKochi\SitemapKit\Models;

use Illuminate\Database\Eloquent\Model;

class MissingUrlLog extends Model
{
    protected $table = 'missing_url_logs';

    protected $fillable = [
        'url_hash',
        'url',
        'referer',
        'hit_count',
        'first_seen_at',
        'last_seen_at',
    ];

    protected $casts = [
        'hit_count' => 'integer',
        'first_seen_at' => 'datetime',
        'last_seen_at' => 'datetime',
    ];
}
