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
        <title>Edit Sitemap - Sitemap Kit</title>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Fira+Code:wght@400;500&display=swap" rel="stylesheet">
    </head>
    <body style="background-color: #f4f7f6; margin: 0; min-height: 100vh; padding: 2rem;">
@endif

@if($layout)
    @section($section)
@endif

@if($stylesEnabled)
    <style>
        .edit-container {
            font-family: 'Inter', sans-serif;
            background: white;
            padding: 2rem;
            border-radius: 1.5rem;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            max-width: 1000px;
            width: 100%;
            margin: 0 auto;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }
        .header h1 {
            color: #1a202c;
            font-size: 1.5rem;
            font-weight: 600;
            margin: 0;
        }
        .editor-wrapper {
            position: relative;
            margin-bottom: 2rem;
        }
        .xml-editor {
            width: 100%;
            height: 500px;
            padding: 1rem;
            border-radius: 0.75rem;
            border: 1px solid #e2e8f0;
            font-family: 'Fira Code', monospace;
            font-size: 0.875rem;
            line-height: 1.6;
            color: #2d3748;
            background-color: #f8fafc;
            resize: vertical;
            outline: none;
            transition: border-color 0.2s;
        }
        .xml-editor:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }
        .actions {
            display: flex;
            gap: 1rem;
            justify-content: flex-end;
        }
        .btn {
            display: inline-flex;
            align-items: center;
            padding: 0.75rem 1.5rem;
            border-radius: 0.75rem;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.2s;
            cursor: pointer;
            border: none;
        }
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }
        .btn-primary:hover {
            transform: translateY(-1px);
            opacity: 0.9;
        }
        .btn-secondary {
            background-color: #edf2f7;
            color: #4a5568;
        }
        .btn-secondary:hover {
            background-color: #e2e8f0;
        }
        .alert {
            margin-bottom: 1.5rem;
            padding: 1rem;
            border-radius: 0.5rem;
            font-size: 0.875rem;
        }
        .alert-error {
            background-color: #fef2f2;
            color: #991b1b;
            border: 1px solid #fecaca;
        }
    </style>
@endif

<div class="edit-container">
    <div class="header">
        <h1>Edit Sitemap Manually</h1>
        <a href="{{ route('sitemap.index') }}" class="btn btn-secondary">
            <svg style="width: 1.25rem; height: 1.25rem; margin-right: 0.5rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Back to Manager
        </a>
    </div>

    @if($errors->any())
        <div class="alert alert-error">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('sitemap.update') }}" method="POST">
        @csrf
        <div class="editor-wrapper">
            <textarea name="content" class="xml-editor" spellcheck="false">{{ old('content', $content) }}</textarea>
        </div>

        <div class="actions">
            <button type="submit" class="btn btn-primary">
                Save Changes
                <svg style="width: 1.25rem; height: 1.25rem; margin-left: 0.5rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path>
                </svg>
            </button>
        </div>
    </form>
</div>

@if($layout)
    @endsection
@else
    </body>
    </html>
@endif
