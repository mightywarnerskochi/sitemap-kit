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
        <title>Add redirect</title>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    </head>
    <body style="background-color: #f4f7f6; margin: 0; min-height: 100vh; padding: 2rem;">
@endif

@if($layout)
    @section($section)
@endif

@if($stylesEnabled)
    <style>
        .sa-wrap { font-family: 'Inter', sans-serif; max-width: 720px; margin: 0 auto; }
        .sa-card { background: white; padding: 2rem; border-radius: 1.25rem; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.08); }
        .sa-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem; }
        .sa-header h1 { margin: 0; font-size: 1.5rem; color: #1a202c; }
        .sa-btn { display: inline-flex; align-items: center; padding: 0.65rem 1.25rem; border-radius: 0.75rem; font-weight: 600; text-decoration: none; border: none; cursor: pointer; font-size: 0.9rem; }
        .sa-btn-primary { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; }
        .sa-btn-secondary { background: #edf2f7; color: #4a5568; }
        .sa-form-row { margin-bottom: 1rem; }
        .sa-form-row label { display: block; font-size: 0.8rem; font-weight: 600; color: #4a5568; margin-bottom: 0.35rem; }
        .sa-input, .sa-select, .sa-textarea { width: 100%; box-sizing: border-box; padding: 0.65rem 0.75rem; border-radius: 0.5rem; border: 1px solid #e2e8f0; font-family: inherit; font-size: 0.9rem; }
        .sa-textarea { min-height: 90px; resize: vertical; }
        .sa-muted { color: #718096; font-size: 0.8rem; margin-top: 0.25rem; }
        .sa-alert-error { background: #fef2f2; color: #991b1b; border: 1px solid #fecaca; padding: 0.85rem 1rem; border-radius: 0.5rem; margin-bottom: 1rem; font-size: 0.875rem; }
    </style>
@endif

<div class="sa-wrap sa-card">
    <div class="sa-header">
        <h1>Add redirect</h1>
        <a href="{{ route('sitemap.redirects.index') }}" class="sa-btn sa-btn-secondary">Back</a>
    </div>

    @if ($errors->any())
        <div class="sa-alert-error">
            <ul style="margin:0; padding-left:1.1rem;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="post" action="{{ route('sitemap.redirects.store') }}">
        @csrf
        <div class="sa-form-row">
            <label for="old_url">Old URL (path)</label>
            <input id="old_url" class="sa-input" name="old_url" required value="{{ old('old_url', request('old_url')) }}" placeholder="/blog/old-slug">
            <div class="sa-muted">Use a site path starting with <code>/</code>. Query strings are ignored when matching.</div>
        </div>
        <div class="sa-form-row">
            <label for="new_url">New URL</label>
            <input id="new_url" class="sa-input" name="new_url" value="{{ old('new_url') }}" placeholder="/blog/new-slug or https://…">
            <div class="sa-muted">Leave empty when status is 410 Gone.</div>
        </div>
        <div class="sa-form-row">
            <label for="status_code">Status code</label>
            <select id="status_code" class="sa-select" name="status_code">
                @foreach ([301,302,307,308,410] as $code)
                    <option value="{{ $code }}" {{ (int) old('status_code', 301) === $code ? 'selected' : '' }}>{{ $code }}</option>
                @endforeach
            </select>
        </div>
        <div class="sa-form-row">
            <label for="notes">Notes (optional)</label>
            <textarea id="notes" class="sa-textarea" name="notes">{{ old('notes') }}</textarea>
        </div>
        <button type="submit" class="sa-btn sa-btn-primary">Save</button>
    </form>
</div>

@if($layout)
    @endsection
@else
    </body>
    </html>
@endif
