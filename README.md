# Sitemap Kit for Laravel

A reusable Laravel package for automatic sitemap generation. This package utilizes `spatie/laravel-sitemap` to handle static and dynamic URLs, providing automated updates via model observers and a manual Artisan command.

## Features

- **Automated Updates**: Automatically regenerate `sitemap.xml` when specific models are created, updated, or deleted.
- **Manual Generation**: Artisan command to regenerate the sitemap on demand.
- **Manual Editing**: Premium admin interface to manually edit the `sitemap.xml` file.
- **Admin Interface**: A simple admin page to trigger sitemap regeneration and view status.
- **Queued Jobs**: Sitemap updates are handled via background jobs to ensure performance.
- **URL Redirects & 404 Log**: Database-backed 301/302/307/308/410 handling with hit counts, automatic rules on slug change/delete, and optional logging of 404 URLs with referer.

## Requirements

- PHP: `^8.1`
- Laravel: `^10.0` or `^11.0`

## Installation

### 1. Install via Composer

Since this is a custom package, add the VCS repository entry to your project's `composer.json`:

```json
"repositories": [
    {
        "type": "vcs",
        "url": "https://github.com/mightywarnerskochi/sitemap-kit"
    }
]
```

Then install it with Composer:

```bash
composer require mightywarnerskochi/sitemap-kit:dev-main
```

### 2. Publish Configuration

Publish the configuration file to customize which models trigger sitemap updates, redirects, and admin middleware:

```bash
php artisan vendor:publish --tag="sitemap-config"
```

This will create a `config/sitemap_automation.php` file in your main project.

### 2b. Run migrations (redirects & 404 log)

The package registers its migrations automatically. Run:

```bash
php artisan migrate
```

This creates `url_redirects` and `missing_url_logs` (automatic 404 logging).

### 3. Publish Views (Optional)

If you want to customize the sitemap management page, you can publish the views:

```bash
php artisan vendor:publish --tag="sitemap-kit-views"
```

The views will be published to `resources/views/vendor/sitemap-automation`.

### 4. Dynamic Layout and Styling

You can configure the management page to blend into your admin panel in `config/sitemap_automation.php`:

```php
'layout' => 'layouts.admin', // Extend your project's admin layout
'section' => 'content',      // The @section name in your layout
'styles_enabled' => true,    // Set to false to use your own CSS
```

### 5. Add to your App

The service provider is automatically registered via Laravel's package discovery. If you need to add it manually, add this to your `config/app.php` providers array:

```php
MightyWarnersKochi\SitemapKit\SitemapAutomationServiceProvider::class,
```

## Usage

### Automated Updates

In your `config/sitemap_automation.php`, list the models you want to observe:

```php
return [
    'models' => [
        \App\Models\Blog::class,
        \App\Models\Product::class,
    ],
];
```

The package will now automatically queue a sitemap update whenever these models change.

### Manual Generation

To manually regenerate the sitemap, run:

```bash
php artisan sitemap:generate
```

### Manual Trigger via HTTP

The package provides routes to manage the sitemap from your own admin panel:

- **Management Board**: `admin/sitemap` (GET) - Name: `sitemap.index`
- **Regenerate**: `admin/sitemap/generate` (GET) - Name: `sitemap.generate`
- **Edit Manual**: `admin/sitemap/edit` (GET) - Name: `sitemap.edit`
- **Update Manual**: `admin/sitemap/update` (POST) - Name: `sitemap.update`
- **Redirects**: `admin/sitemap/redirects` (GET) - Name: `sitemap.redirects.index`
- **404 log**: `admin/sitemap/missing-urls` (GET) - Name: `sitemap.missing-urls.index`

*Note: These routes use the middleware defined in your configuration (default `web`). Add `auth` or your admin gate to the `middleware` array in `config/sitemap_automation.php` when exposing this UI in production.*

### URL redirects & 404 logging

The package can manage SEO redirects in the database (instead of `.htaccess`) and log unknown URLs that return 404.

**Why “sitemap” in the config filename?** The package publishes one file, `config/sitemap_automation.php`. Redirect and 404 settings live there for convenience only—they are **not** tied to the sitemap or to Eloquent models. Static pages (about, contact, legal, campaign URLs) are first-class: use the admin UI, `path_redirects` in config, or both.

- **Global middleware** (`ResolveUrlRedirects`) is prepended automatically when `redirects.enabled` and `redirects.register_global_middleware` are true. It runs *before* routing so old indexed paths still match and receive 301/302/307/308 or **410 Gone** from your rules.
- **`path_redirects`** (in `redirects`): config-only rules for fixed paths—no database, no models. Good for about/contact renames you want in git. **A row in `url_redirects` overrides the same `old_url`** if both exist (so ops can override deploy-time rules).
- **Admin / database**: `admin/sitemap/redirects` for any URL pair, hit counts, and status codes; `admin/sitemap/missing-urls` for the 404 log (“Create redirect” pre-fills the old path).
- **Optional model observer** (`UrlRedirectObserver`): when a configured model’s slug changes, a **301** is stored automatically. On delete, use `on_delete.strategy` (`listing`, `gone`, `url`, `relation`, or `model_method`). Enable with `redirects.models` and/or `observe_sitemap_models`.
- **Config**: `redirects` and `not_found_logging` in `config/sitemap_automation.php`. Set `except_path_prefixes` so `/admin` (and similar) are not checked against redirect rules or logged as 404s.
- To **disable** automatic global middleware (e.g. custom ordering), set `register_global_middleware` to `false` and register `MightyWarnersKochi\SitemapKit\Http\Middleware\ResolveUrlRedirects` yourself via `$kernel->prependMiddleware(...)` in your application bootstrap so it still runs before the router.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

## Authors

- **mightywarnerskochi** - [support@mightywarnerskochi.com]
