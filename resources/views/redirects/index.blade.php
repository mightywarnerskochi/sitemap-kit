@php
    $layout = config('sitemap_automation.layout');
    $section = config('sitemap_automation.section', 'content');
    $stylesEnabled = config('sitemap_automation.styles_enabled', true);
@endphp

@if($layout)
    @extends($layout)
@else
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>URL Redirects</title>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    </head>
    <body style="background-color: #f4f7f6; margin: 0; min-height: 100vh; padding: 2rem;">
@endif

@if($layout)
    @section($section)
@endif

@if($stylesEnabled)
    <style>
        .sk-wrap { font-family: 'Inter', sans-serif; max-width: 1120px; margin: 0 auto; }
        .sk-card { background: #fff; padding: 1.75rem 1.5rem; border-radius: 1rem; box-shadow: 0 4px 24px rgba(15, 23, 42, 0.06); border: 1px solid #e2e8f0; }
        .sk-head { display: flex; align-items: flex-start; justify-content: space-between; gap: 1rem; flex-wrap: wrap; margin-bottom: 1.25rem; }
        .sk-head h1 { margin: 0; font-size: 1.35rem; font-weight: 600; color: #0f172a; letter-spacing: -0.02em; }
        .sk-nav { display: flex; gap: 0.4rem; align-items: center; flex-shrink: 0; }
        .sk-icon-btn {
            display: inline-flex; align-items: center; justify-content: center;
            width: 2.5rem; height: 2.5rem; border-radius: 0.6rem; border: 1px solid #e2e8f0;
            background: #f8fafc; color: #475569; text-decoration: none;
            transition: background 0.15s, border-color 0.15s, color 0.15s;
        }
        .sk-icon-btn:hover { background: #fff; border-color: #c7d2fe; color: #4f46e5; }
        .sk-icon-btn--primary { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none; color: #fff; }
        .sk-icon-btn--primary:hover { opacity: 0.95; color: #fff; border: none; }
        .sk-icon-btn svg { width: 1.15rem; height: 1.15rem; }
        button.sk-icon-btn { font: inherit; cursor: pointer; }
        .sk-icon-btn--cache { border-color: #fcd34d; background: #fffbeb; color: #b45309; }
        .sk-icon-btn--cache:hover { border-color: #f59e0b; color: #92400e; background: #fff7ed; }
        .sk-filter { display: flex; gap: 0.5rem; align-items: flex-end; margin-bottom: 1rem; flex-wrap: wrap; }
        .sk-filter label { display: block; font-size: 0.72rem; font-weight: 600; color: #64748b; text-transform: uppercase; letter-spacing: 0.04em; margin-bottom: 0.35rem; }
        .sk-filter-grow { flex: 1; min-width: 200px; }
        .sk-input { width: 100%; box-sizing: border-box; padding: 0.55rem 0.75rem; border-radius: 0.5rem; border: 1px solid #e2e8f0; font-size: 0.875rem; }
        .sk-btn-text { padding: 0.55rem 1rem; border-radius: 0.5rem; border: 1px solid #e2e8f0; background: #f8fafc; font-weight: 600; font-size: 0.8125rem; color: #475569; cursor: pointer; }
        .sk-btn-text:hover { background: #f1f5f9; }
        .sk-table-wrap { border: 1px solid #e2e8f0; border-radius: 0.75rem; overflow: hidden; }
        .sk-table { width: 100%; border-collapse: collapse; table-layout: fixed; font-size: 0.8125rem; }
        .sk-table th { text-align: left; padding: 0.65rem 0.75rem; background: #f8fafc; color: #64748b; font-weight: 600; font-size: 0.65rem; text-transform: uppercase; letter-spacing: 0.05em; border-bottom: 1px solid #e2e8f0; }
        .sk-table td { padding: 0.65rem 0.75rem; border-bottom: 1px solid #f1f5f9; vertical-align: middle; }
        .sk-table tr:last-child td { border-bottom: none; }
        .sk-col-url { width: 32%; }
        .sk-col-meta { width: 7%; text-align: center; }
        .sk-col-src { width: 10%; }
        .sk-col-act { width: 4.5rem; text-align: right; }
        .sk-url { display: block; font-family: ui-monospace, monospace; font-size: 0.75rem; line-height: 1.45; color: #334155; overflow-wrap: anywhere; word-break: break-word; max-height: 4.5em; overflow: hidden; }
        .sk-badge { display: inline-block; padding: 0.2rem 0.45rem; border-radius: 9999px; font-size: 0.7rem; font-weight: 600; background: #f1f5f9; color: #475569; }
        .sk-act { display: inline-flex; gap: 0.25rem; justify-content: flex-end; }
        .sk-act-btn {
            display: inline-flex; align-items: center; justify-content: center;
            width: 2.1rem; height: 2.1rem; border-radius: 0.45rem; border: 1px solid #e2e8f0;
            background: #fff; color: #64748b; text-decoration: none; padding: 0; cursor: pointer;
        }
        .sk-act-btn:hover { border-color: #a5b4fc; color: #4f46e5; background: #faf5ff; }
        .sk-act-btn--danger:hover { border-color: #fecaca; color: #b91c1c; background: #fef2f2; }
        .sk-act-btn svg { width: 1rem; height: 1rem; }
        .sk-alert { margin-bottom: 1rem; padding: 0.75rem 1rem; border-radius: 0.5rem; font-size: 0.875rem; background: #f0fdf4; color: #166534; border: 1px solid #bbf7d0; }
        .sk-trouble { font-size: 0.8125rem; color: #64748b; line-height: 1.55; margin-bottom: 1rem; padding: 0.75rem 1rem; background: #fffbeb; border: 1px solid #fde68a; border-radius: 0.5rem; }
        .sk-empty { padding: 2rem; text-align: center; color: #64748b; font-size: 0.875rem; }
        .sr-only { position: absolute; width: 1px; height: 1px; padding: 0; margin: -1px; overflow: hidden; clip: rect(0,0,0,0); white-space: nowrap; border: 0; }
    </style>
@endif

<div class="sk-wrap sk-card">
    <div class="sk-head">
        <h1>URL Redirects</h1>
        <nav class="sk-nav" aria-label="Section navigation">
            <a href="{{ route('sitemap.redirects.create') }}" class="sk-icon-btn sk-icon-btn--primary" title="Add redirect">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            </a>
            <a href="{{ route('sitemap.missing-urls.index') }}" class="sk-icon-btn" title="404 URL log">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            </a>
            <a href="{{ route('sitemap.index') }}" class="sk-icon-btn" title="Sitemap">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
            </a>
            @if(config('sitemap_automation.redirects.allow_optimize_clear_from_admin', true))
                <form method="post" action="{{ route('sitemap.redirects.optimize-clear') }}" style="display:inline;margin:0;" onsubmit="return confirm('Run php artisan optimize:clear? This clears config, route, view, and compiled caches.');">
                    @csrf
                    <button type="submit" class="sk-icon-btn sk-icon-btn--cache" title="Optimize clear (php artisan optimize:clear)">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    </button>
                </form>
            @endif
        </nav>
    </div>

    @if(session('success'))
        <div class="sk-alert">{{ session('success') }}</div>
    @endif

    <p class="sk-trouble">
        <strong>Removed a rule but the URL still redirects?</strong> This package reads <code>url_redirects</code> on each request—there is no in-memory redirect cache in the package. Common causes: <strong>browser cache</strong> for 301, a row you did not delete (search the table), <code>path_redirects</code> in config, <strong>web server</strong> rewrites, Laravel <code>Route::redirect</code>, or an <strong>automatic redirect observer</strong> recreating a rule when content is saved. Use the toolbar <strong>Optimize clear</strong> (lightning) after config or route changes, or run <code>php artisan optimize:clear</code> in the terminal.
    </p>

    <form method="get" action="{{ route('sitemap.redirects.index') }}" class="sk-filter">
        <div class="sk-filter-grow">
            <label for="q">Search</label>
            <input id="q" class="sk-input" type="text" name="q" value="{{ request('q') }}" placeholder="Old or new URL contains…">
        </div>
        <button type="submit" class="sk-btn-text">Filter</button>
    </form>

    <div class="sk-table-wrap">
        <table class="sk-table">
            <thead>
                <tr>
                    <th class="sk-col-url">Old URL</th>
                    <th class="sk-col-url">New URL</th>
                    <th class="sk-col-meta">Code</th>
                    <th class="sk-col-meta">Hits</th>
                    <th class="sk-col-src">Source</th>
                    <th class="sk-col-act"><span class="sr-only">Actions</span></th>
                </tr>
            </thead>
            <tbody>
                @forelse($redirects as $row)
                    <tr>
                        <td><span class="sk-url" title="{{ $row->old_url }}">{{ $row->old_url }}</span></td>
                        <td>
                            @if($row->status_code === 410)
                                <span class="sk-badge">410 Gone</span>
                            @else
                                <span class="sk-url" title="{{ $row->new_url }}">{{ $row->new_url }}</span>
                            @endif
                        </td>
                        <td class="sk-col-meta"><span class="sk-badge">{{ $row->status_code }}</span></td>
                        <td class="sk-col-meta">{{ number_format($row->hit_count) }}</td>
                        <td><span class="sk-badge">{{ $row->source }}</span></td>
                        <td class="sk-col-act">
                            <div class="sk-act">
                                <a class="sk-act-btn" href="{{ route('sitemap.redirects.edit', $row) }}" title="Edit">
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                </a>
                                <form action="{{ route('sitemap.redirects.destroy', $row) }}" method="post" style="display:inline;margin:0;" onsubmit="return confirm('Delete this redirect?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="sk-act-btn sk-act-btn--danger" title="Delete">
                                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="sk-empty">No redirects yet. Add rules manually or enable automatic rules on your models.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top:1rem;">
        {{ $redirects->links() }}
    </div>
</div>

@if($layout)
    @endsection
@else
    </body>
    </html>
@endif
