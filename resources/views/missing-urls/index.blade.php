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
        <title>404 URL log</title>
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
        .sk-head { display: flex; align-items: flex-start; justify-content: space-between; gap: 1rem; flex-wrap: wrap; margin-bottom: 1rem; }
        .sk-head h1 { margin: 0; font-size: 1.35rem; font-weight: 600; color: #0f172a; letter-spacing: -0.02em; }
        .sk-nav { display: flex; gap: 0.4rem; align-items: center; flex-shrink: 0; }
        .sk-icon-btn {
            display: inline-flex; align-items: center; justify-content: center;
            width: 2.5rem; height: 2.5rem; border-radius: 0.6rem; border: 1px solid #e2e8f0;
            background: #f8fafc; color: #475569; text-decoration: none;
            transition: background 0.15s, border-color 0.15s, color 0.15s;
        }
        .sk-icon-btn:hover { background: #fff; border-color: #c7d2fe; color: #4f46e5; }
        .sk-icon-btn svg { width: 1.15rem; height: 1.15rem; }
        .sk-hint { font-size: 0.875rem; color: #475569; line-height: 1.6; margin-bottom: 1rem; padding: 1rem 1.1rem; background: #f8fafc; border-radius: 0.65rem; border: 1px solid #e2e8f0; }
        .sk-hint-title { display: block; font-size: 0.9375rem; font-weight: 600; color: #1e293b; margin-bottom: 0.5rem; }
        .sk-hint ul { margin: 0.35rem 0 0; padding-left: 1.2rem; }
        .sk-hint li { margin-bottom: 0.4rem; }
        .sk-hint li:last-child { margin-bottom: 0; }
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
        .sk-col-path { width: 38%; }
        .sk-col-ref { width: 34%; }
        .sk-col-num { width: 8%; text-align: center; }
        .sk-col-act { width: 4.5rem; text-align: right; }
        .sk-url { display: block; font-family: ui-monospace, monospace; font-size: 0.75rem; line-height: 1.45; color: #334155; overflow-wrap: anywhere; word-break: break-word; max-height: 4.5em; overflow: hidden; }
        .sk-ref { display: block; font-size: 0.72rem; color: #64748b; overflow-wrap: anywhere; max-height: 3.2em; overflow: hidden; }
        .sk-badge { display: inline-block; padding: 0.2rem 0.45rem; border-radius: 9999px; font-size: 0.7rem; font-weight: 600; background: #f1f5f9; color: #475569; }
        .sk-act { display: inline-flex; gap: 0.25rem; justify-content: flex-end; }
        .sk-act-btn {
            display: inline-flex; align-items: center; justify-content: center;
            width: 2.1rem; height: 2.1rem; border-radius: 0.45rem; border: 1px solid #e2e8f0;
            background: #fff; color: #64748b; text-decoration: none; padding: 0; cursor: pointer;
        }
        .sk-act-btn:hover { border-color: #a5b4fc; color: #4f46e5; background: #faf5ff; }
        .sk-act-btn--primary { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none; color: #fff; }
        .sk-act-btn--primary:hover { opacity: 0.95; color: #fff; border: none; }
        .sk-act-btn--danger:hover { border-color: #fecaca; color: #b91c1c; background: #fef2f2; }
        .sk-act-btn svg { width: 1rem; height: 1rem; }
        .sk-alert { margin-bottom: 1rem; padding: 0.75rem 1rem; border-radius: 0.5rem; font-size: 0.875rem; background: #f0fdf4; color: #166534; border: 1px solid #bbf7d0; }
        .sk-alert-error { background: #fef2f2; color: #991b1b; border: 1px solid #fecaca; }
        .sk-toolbar { display: flex; justify-content: flex-end; margin-bottom: 1rem; flex-wrap: wrap; gap: 0.75rem; align-items: center; }
        .sk-clear-form { display: flex; align-items: center; gap: 0.75rem; flex-wrap: wrap; }
        .sk-check { display: inline-flex; align-items: center; gap: 0.4rem; font-size: 0.8125rem; color: #475569; cursor: pointer; user-select: none; }
        .sk-check input { width: 1rem; height: 1rem; accent-color: #4f46e5; }
        .sk-btn-clear {
            padding: 0.5rem 1rem; border-radius: 0.5rem; font-weight: 600; font-size: 0.8125rem;
            border: 1px solid #fecaca; background: #fff; color: #b91c1c; cursor: pointer;
        }
        .sk-btn-clear:hover { background: #fef2f2; }
        .sk-empty { padding: 2rem; text-align: center; color: #64748b; font-size: 0.875rem; line-height: 1.6; }
        .sr-only { position: absolute; width: 1px; height: 1px; padding: 0; margin: -1px; overflow: hidden; clip: rect(0,0,0,0); white-space: nowrap; border: 0; }
    </style>
@endif

<div class="sk-wrap sk-card">
    <div class="sk-head">
        <h1>404 URL log</h1>
        <nav class="sk-nav" aria-label="Section navigation">
            <a href="{{ route('sitemap.redirects.index') }}" class="sk-icon-btn" title="URL redirects">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4M16 17H4m0 0l4 4m-4-4l4-4"/></svg>
            </a>
            <a href="{{ route('sitemap.index') }}" class="sk-icon-btn" title="Sitemap">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
            </a>
        </nav>
    </div>

    <div class="sk-hint" role="note">
        <span class="sk-hint-title">How this log helps SEO &amp; content</span>
        This page lists <strong>addresses that returned “page not found”</strong> to real visitors. Use it to find broken links, old campaigns, or typos that still get clicks from Google or other sites.
        <ul>
            <li><strong>Healthy pages do not belong here.</strong> If a URL opens the correct article or section, it will not be listed—that means the visit was successful, not an error.</li>
            <li><strong>Successful redirects are not errors.</strong> If an old URL sends people to the new URL using a rule under <em>URL Redirects</em>, that visit is good for users and SEO and <strong>will not</strong> appear in this log.</li>
            <li><strong>Testing after you change something?</strong> If your browser still jumps to an old page even though the redirect was removed, try a <strong>private / incognito window</strong> or another browser—browsers often remember “permanent” moves for a long time.</li>
            <li><strong>Nothing new showing up?</strong> Ask your developer to confirm the visit really ends as “not found” (not a normal page load) and that the site is on the latest version of this tool if you recently updated it. For stuck settings, they can use the <strong>yellow lightning</strong> button on the <em>URL Redirects</em> page to refresh the site’s internal cache.</li>
        </ul>
    </div>

    @if(session('success'))
        <div class="sk-alert">{{ session('success') }}</div>
    @endif

    @if ($errors->any())
        <div class="sk-alert sk-alert-error" role="alert">
            <ul style="margin:0;padding-left:1.2rem;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="sk-toolbar">
        <form method="post" action="{{ route('sitemap.missing-urls.clear') }}" class="sk-clear-form" onsubmit="return confirm('Delete every row in the 404 log? This cannot be undone.');">
            @csrf
            <label class="sk-check">
                <input type="checkbox" name="confirm_clear" value="1">
                <span>Confirm clear</span>
            </label>
            <button type="submit" class="sk-btn-clear">Clear log</button>
        </form>
    </div>

    <form method="get" action="{{ route('sitemap.missing-urls.index') }}" class="sk-filter">
        <div class="sk-filter-grow">
            <label for="q">Search</label>
            <input id="q" class="sk-input" type="text" name="q" value="{{ request('q') }}" placeholder="Path contains…">
        </div>
        <button type="submit" class="sk-btn-text">Filter</button>
    </form>

    <div class="sk-table-wrap">
        <table class="sk-table">
            <thead>
                <tr>
                    <th class="sk-col-path">URL</th>
                    <th class="sk-col-ref">Referer</th>
                    <th class="sk-col-num">Hits</th>
                    <th class="sk-col-num">Last seen</th>
                    <th class="sk-col-act"><span class="sr-only">Actions</span></th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $log)
                    <tr>
                        <td><span class="sk-url" title="{{ $log->url }}">{{ $log->url }}</span></td>
                        <td>
                            @if($log->referer)
                                <span class="sk-ref" title="{{ $log->referer }}">{{ $log->referer }}</span>
                            @else
                                <span class="sk-badge">—</span>
                            @endif
                        </td>
                        <td class="sk-col-num">{{ number_format($log->hit_count) }}</td>
                        <td class="sk-col-num" style="font-size:0.75rem;color:#64748b;">{{ $log->last_seen_at }}</td>
                        <td class="sk-col-act">
                            <div class="sk-act">
                                <a class="sk-act-btn sk-act-btn--primary" href="{{ route('sitemap.missing-urls.promote', $log) }}" title="Create redirect from this URL">
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                                </a>
                                <form action="{{ route('sitemap.missing-urls.destroy', $log) }}" method="post" style="display:inline;margin:0;" onsubmit="return confirm('Remove this log entry?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="sk-act-btn sk-act-btn--danger" title="Delete log entry">
                                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="sk-empty">No broken addresses recorded yet—or logging is turned off. Remember: working pages and successful redirects never appear here; only real “page not found” visits do.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top:1rem;">
        {{ $logs->links() }}
    </div>
</div>

@if($layout)
    @endsection
@else
    </body>
    </html>
@endif
