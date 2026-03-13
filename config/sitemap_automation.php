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
        // \App\Models\Blog::class,
        // \App\Models\Product::class,
        // \App\Models\Service::class,
        // \App\Models\Career::class,
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

    'middleware' => [],

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
