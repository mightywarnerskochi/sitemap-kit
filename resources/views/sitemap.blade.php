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
        <title>Sitemap Generator</title>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    </head>
    <body style="background-color: #f4f7f6; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0;">
@endif

@if($layout)
    @section($section)
@endif

@if($stylesEnabled)
    <style>
        .sitemap-card {
            font-family: 'Inter', sans-serif;
            background: white;
            padding: 2.5rem;
            border-radius: 1.5rem;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            max-width: 650px;
            width: 100%;
            text-align: center;
            transition: transform 0.2s ease;
            margin: 2rem auto;
        }
        .sitemap-card:hover {
            transform: translateY(-5px);
        }
        .sitemap-card h1 {
            color: #1a202c;
            font-size: 1.875rem;
            font-weight: 600;
            margin-bottom: 1rem;
        }
        .sitemap-card p {
            color: #4a5568;
            margin-bottom: 2rem;
            line-height: 1.5;
        }
        .sitemap-status {
            display: inline-flex;
            align-items: center;
            padding: 0.5rem 1rem;
            border-radius: 9999px;
            font-size: 0.875rem;
            font-weight: 500;
            margin-bottom: 2rem;
        }
        .sitemap-status-exists {
            background-color: #def7ec;
            color: #03543f;
        }
        .sitemap-status-missing {
            background-color: #fde8e8;
            color: #9b1c1c;
        }
        .sitemap-btn {
            display: inline-block;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 0.875rem 2rem;
            border-radius: 0.75rem;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            cursor: pointer;
            border: none;
            width: 100%;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }
        .sitemap-btn:hover {
            opacity: 0.9;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            transform: translateY(-2px);
        }
        .sitemap-alert {
            margin-top: 1.5rem;
            padding: 1rem;
            border-radius: 0.5rem;
            font-size: 0.875rem;
        }
        .sitemap-alert-success {
            background-color: #f0fdf4;
            color: #166534;
            border: 1px solid #bbf7d0;
        }
    </style>
@endif

<div class="sitemap-card">
    <h1>Sitemap Management</h1>
    
    @if($exists)
        <div class="sitemap-status sitemap-status-exists">
            <svg style="width: 1.25rem; height: 1.25rem; margin-right: 0.5rem;" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
            </svg>
            sitemap.xml exists
        </div>
    @else
        <div class="sitemap-status sitemap-status-missing">
            <svg style="width: 1.25rem; height: 1.25rem; margin-right: 0.5rem;" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
            </svg>
            sitemap.xml is missing
        </div>
    @endif

    <p>
        Automatically generate and update your sitemap to improve SEO. 
        The system already monitors content changes, but you can manually trigger a full crawl here.
    </p>

    <div class="sitemap-actions" style="display: flex; gap: 1rem; margin-top: 1rem; flex-wrap: wrap;">
        <form action="{{ route('sitemap.generate') }}" method="GET" style="flex: 1; min-width: 200px;">
            <button type="submit" class="sitemap-btn">
                {{ $exists ? 'Regenerate Sitemap' : 'Generate Sitemap' }}
                <svg style="width: 1.25rem; height: 1.25rem; margin-left: 0.5rem; vertical-align: middle;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                </svg>
            </button>
        </form>

        @if($exists)
            <a href="{{ route('sitemap.edit') }}" class="sitemap-btn" style="flex: 1; min-width: 200px; background: #ed8936; display: flex; align-items: center; justify-content: center; text-decoration: none;">
                Edit Manual
                <svg style="width: 1.25rem; height: 1.25rem; margin-left: 0.5rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                </svg>
            </a>

            <a href="{{ url('sitemap.xml') }}" target="_blank" class="sitemap-btn" style="flex: 1; min-width: 200px; background: #4a5568; display: flex; align-items: center; justify-content: center; text-decoration: none;">
                View Sitemap
                <svg style="width: 1.25rem; height: 1.25rem; margin-left: 0.5rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                </svg>
            </a>
        @endif
    </div>

    @if(session('success'))
        <div class="sitemap-alert sitemap-alert-success">
            {{ session('success') }}
        </div>
    @endif
</div>

@if($layout)
    @endsection
@else
    </body>
    </html>
@endif
