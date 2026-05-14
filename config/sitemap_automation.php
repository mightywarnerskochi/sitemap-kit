<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Observed Models
    |--------------------------------------------------------------------------
    |
    | List the models that should trigger an automated sitemap update when
    | they are created, updated, or deleted.
    |
    */

    'models' => [
        // Simple format (requires getSitemapUrl() or 'url' attribute on model)
        // \App\Models\Blog::class,

        // Dynamic format (specify URL prefix and optionally slug field)
        // \App\Models\Service::class => [
        //     'url_prefix' => '/services/',
        //     'slug_field' => 'slug', // optional, defaults to 'slug'
        // ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Route Middleware
    |--------------------------------------------------------------------------
    |
    | Define the middleware that should be applied to the sitemap admin routes.
    | Default is ['web', 'auth']. If your project uses a different
    | authentication middleware, update it here.
    |
    */

    'middleware' => ['web'],

    /*
    |--------------------------------------------------------------------------
    | URL redirects & 404 logging
    |--------------------------------------------------------------------------
    |
    | These settings live in this file only because the package ships one merged
    | config—they are not limited to "sitemap" or to Eloquent models. You get:
    |
    | - path_redirects: static/marketing URLs (about, contact, landing pages)
    |   without any model—good for git-reviewed rules.
    | - Database rules (admin UI): arbitrary old→new pairs, hit counts, 410s.
    | - redirects.models / observe_sitemap_models: optional automation when
    |   blog/product slugs change or rows are deleted.
    |
    | Database rules win over path_redirects when both define the same old_url.
    |
    */

    'redirects' => [
        'enabled' => true,
        'register_global_middleware' => true,
        'observe_sitemap_models' => true,

        /*
        | Static path redirects (no database, no Eloquent). Keys and targets are
        | normalized like DB rules. Value can be:
        | - string: new path or full URL, 301
        | - [newPathOrUrl, statusCode] e.g. ['/contact', 301] or ['', 410]
        | - ['to' => '/x', 'status' => 302] or ['target' => 'https://…', 'status' => 301]
        */
        'path_redirects' => [
            // '/about-old' => '/about',
            // '/reach-us' => ['/contact', 301],
            // '/legacy-promo' => ['to' => '/offers', 'status' => 302],
            // '/retired-page' => ['', 410],
        ],

        'models' => [
            // \App\Models\Blog::class => [
            //     'url_prefix' => '/blog/',
            //     'slug_field' => 'slug',
            //     'on_delete' => [
            //         'strategy' => 'listing', // listing|gone|url|relation|model_method
            //         'listing_path' => '/blog',
            //         // 'target_url' => '/support', // for strategy "url"
            //         // 'relation' => 'category',
            //         // 'relation_slug_field' => 'slug',
            //         // 'relation_path_prefix' => '/categories/',
            //         // 'fallback_strategy' => 'gone', // when relation missing
            //         // 'method' => 'getRedirectUrlAfterDelete', // model_method
            //     ],
            // ],
        ],
        'http_methods' => ['GET', 'HEAD'],
        'except_path_prefixes' => [
            '/admin',
        ],
        'skip_path_suffixes' => [
            // '.xml',
        ],

        /*
        | When true, the URL redirects admin toolbar can run optimize:clear.
        | Set to false in production if you prefer CLI-only cache clears.
        */
        'allow_optimize_clear_from_admin' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | 404 URL logging
    |--------------------------------------------------------------------------
    |
    | Only HTTP responses with status 404 are stored. If ResolveUrlRedirects
    | matches a rule first, the client receives 301/302/410 instead—so that path
    | will not appear in missing_url_logs (use the redirect list and hit_count).
    | Run migrations after package updates (url_hash column improves reliability).
    |
    */

    'not_found_logging' => [
        'enabled' => true,
        'http_methods' => ['GET', 'HEAD'],
        'except_path_prefixes' => [
            '/admin',
        ],
        'skip_extensions' => [
            'ico', 'png', 'jpg', 'jpeg', 'gif', 'webp', 'css', 'js', 'map', 'txt', 'woff', 'woff2', 'ttf',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | View Architecture
    |--------------------------------------------------------------------------
    |
    | If 'layout' is null, the view will be a standalone page.
    | If a layout is provided (e.g., 'layouts.app'), the view will @extend it.
    | 'section' determines where the content will be placed in the layout.
    | Set 'styles_enabled' to false to disable default package CSS.
    |
    */

    'layout' => null, // e.g., 'layouts.app'
    'section' => 'content',
    'styles_enabled' => true,

];
