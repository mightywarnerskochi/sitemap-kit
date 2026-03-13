# Sitemap Kit for Laravel

A reusable Laravel package for automatic sitemap generation. This package utilizes `spatie/laravel-sitemap` to handle static and dynamic URLs, providing automated updates via model observers and a manual Artisan command.

## Features

- **Automated Updates**: Automatically regenerate `sitemap.xml` when specific models are created, updated, or deleted.
- **Manual Generation**: Artisan command to regenerate the sitemap on demand.
- **Manual Editing**: Premium admin interface to manually edit the `sitemap.xml` file.
- **Admin Interface**: A simple admin page to trigger sitemap regeneration and view status.
- **Queued Jobs**: Sitemap updates are handled via background jobs to ensure performance.

## Requirements

- PHP: `^8.1`
- Laravel: `^10.0` or `^11.0`

## Installation

### 1. Install via Composer

Since this is a custom package, you can install it using composer. If it's published on Packagist:

```bash
composer require dev1kochi-crypto/sitemap-kit
```

### 2. Publish Configuration

Publish the configuration file to customize which models trigger sitemap updates:

```bash
php artisan vendor:publish --tag="sitemap-config"
```

This will create a `config/sitemap_automation.php` file in your main project.

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
Dev1kochiCrypto\SitemapKit\SitemapAutomationServiceProvider::class,
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

*Note: These routes are protected by the middleware defined in your configuration (defaulting to `web`).*

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

## Authors

- **dev1kochi-crypto** - [dev1kochi-crypto@example.com]
