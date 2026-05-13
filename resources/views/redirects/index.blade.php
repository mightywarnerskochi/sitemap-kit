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
        .sa-wrap { font-family: 'Inter', sans-serif; max-width: 1100px; margin: 0 auto; }
        .sa-card { background: white; padding: 2rem; border-radius: 1.25rem; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.08); }
        .sa-header { display: flex; justify-content: space-between; align-items: center; gap: 1rem; flex-wrap: wrap; margin-bottom: 1.5rem; }
        .sa-header h1 { margin: 0; font-size: 1.5rem; color: #1a202c; }
        .sa-btn { display: inline-flex; align-items: center; padding: 0.65rem 1.25rem; border-radius: 0.75rem; font-weight: 600; text-decoration: none; border: none; cursor: pointer; font-size: 0.9rem; }
        .sa-btn-primary { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; }
        .sa-btn-secondary { background: #edf2f7; color: #4a5568; }
        .sa-table { width: 100%; border-collapse: collapse; font-size: 0.875rem; }
        .sa-table th, .sa-table td { text-align: left; padding: 0.75rem 0.5rem; border-bottom: 1px solid #e2e8f0; vertical-align: top; word-break: break-word; }
        .sa-table th { color: #718096; font-weight: 600; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.03em; }
        .sa-badge { display: inline-block; padding: 0.2rem 0.5rem; border-radius: 9999px; font-size: 0.75rem; background: #edf2f7; color: #2d3748; }
        .sa-alert { margin-bottom: 1rem; padding: 0.85rem 1rem; border-radius: 0.5rem; font-size: 0.875rem; }
        .sa-alert-success { background: #f0fdf4; color: #166534; border: 1px solid #bbf7d0; }
        .sa-form-row { margin-bottom: 1rem; }
        .sa-form-row label { display: block; font-size: 0.8rem; font-weight: 600; color: #4a5568; margin-bottom: 0.35rem; }
        .sa-input, .sa-select, .sa-textarea { width: 100%; max-width: 100%; box-sizing: border-box; padding: 0.65rem 0.75rem; border-radius: 0.5rem; border: 1px solid #e2e8f0; font-family: inherit; font-size: 0.9rem; }
        .sa-textarea { min-height: 80px; resize: vertical; }
        .sa-muted { color: #718096; font-size: 0.8rem; margin-top: 0.25rem; }
        .sa-actions { display: flex; gap: 0.5rem; flex-wrap: wrap; }
    </style>
@endif

<div class="sa-wrap sa-card">
    <div class="sa-header">
        <h1>URL Redirects</h1>
        <div style="display:flex; gap:0.5rem; flex-wrap:wrap;">
            <a href="{{ route('sitemap.redirects.create') }}" class="sa-btn sa-btn-primary">Add redirect</a>
            <a href="{{ route('sitemap.missing-urls.index') }}" class="sa-btn sa-btn-secondary">404 log</a>
            <a href="{{ route('sitemap.index') }}" class="sa-btn sa-btn-secondary">Sitemap</a>
        </div>
    </div>

    @if(session('success'))
        <div class="sa-alert sa-alert-success">{{ session('success') }}</div>
    @endif

    <form method="get" action="{{ route('sitemap.redirects.index') }}" class="sa-form-row" style="display:flex; gap:0.5rem; align-items:flex-end;">
        <div style="flex:1; min-width:200px;">
            <label for="q">Search</label>
            <input id="q" class="sa-input" type="text" name="q" value="{{ request('q') }}" placeholder="Old or new URL contains…">
        </div>
        <button type="submit" class="sa-btn sa-btn-secondary">Filter</button>
    </form>

    <div style="overflow-x:auto;">
        <table class="sa-table">
            <thead>
                <tr>
                    <th>Old URL</th>
                    <th>New URL</th>
                    <th>Code</th>
                    <th>Hits</th>
                    <th>Source</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($redirects as $row)
                    <tr>
                        <td><code>{{ $row->old_url }}</code></td>
                        <td>@if($row->status_code === 410)<span class="sa-badge">410 Gone</span>@else<code>{{ $row->new_url }}</code>@endif</td>
                        <td><span class="sa-badge">{{ $row->status_code }}</span></td>
                        <td>{{ number_format($row->hit_count) }}</td>
                        <td>{{ $row->source }}</td>
                        <td>
                            <div class="sa-actions">
                                <a class="sa-btn sa-btn-secondary" href="{{ route('sitemap.redirects.edit', $row) }}">Edit</a>
                                <form action="{{ route('sitemap.redirects.destroy', $row) }}" method="post" onsubmit="return confirm('Delete this redirect?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="sa-btn sa-btn-secondary" style="background:#fde8e8;color:#9b1c1c;">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6">No redirects yet. Add rules manually or enable automatic rules on your models.</td></tr>
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
