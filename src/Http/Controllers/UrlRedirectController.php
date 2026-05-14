<?php

namespace MightyWarnersKochi\SitemapKit\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use MightyWarnersKochi\SitemapKit\Models\UrlRedirect;
use MightyWarnersKochi\SitemapKit\Services\RedirectPathNormalizer;
use MightyWarnersKochi\SitemapKit\Services\UrlRedirectService;

class UrlRedirectController extends Controller
{
    /** @var UrlRedirectService */
    protected $redirectService;

    public function __construct(UrlRedirectService $redirectService)
    {
        $this->redirectService = $redirectService;
    }

    public function index(Request $request)
    {
        $query = UrlRedirect::query()->orderByDesc('updated_at');

        if ($search = trim((string) $request->get('q'))) {
            $query->where(function ($q) use ($search) {
                $q->where('old_url', 'like', '%'.$search.'%')
                    ->orWhere('new_url', 'like', '%'.$search.'%');
            });
        }

        $redirects = $query->paginate(25)->withQueryString();

        return view('sitemap-automation::redirects.index', compact('redirects'));
    }

    public function create()
    {
        return view('sitemap-automation::redirects.create');
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $newUrl = $this->prepareNewUrlFromPayload($data);

        $this->redirectService->upsertRule($data['old_url'], $newUrl, (int) $data['status_code'], [
            'notes' => $data['notes'] ?? null,
            'source' => 'manual',
            'created_by' => Auth::id(),
        ]);

        return redirect()->route('sitemap.redirects.index')->with('success', 'Redirect saved.');
    }

    public function edit(UrlRedirect $redirect)
    {
        return view('sitemap-automation::redirects.edit', ['redirect' => $redirect]);
    }

    public function update(Request $request, UrlRedirect $redirect)
    {
        $data = $this->validated($request);
        $newUrl = $this->prepareNewUrlFromPayload($data);

        $redirect->fill([
            'old_url' => RedirectPathNormalizer::normalizePath($data['old_url']),
            'new_url' => $newUrl !== null ? $this->normalizeNewUrl($newUrl) : null,
            'status_code' => (int) $data['status_code'],
            'notes' => $data['notes'] ?? null,
        ])->save();

        return redirect()->route('sitemap.redirects.index')->with('success', 'Redirect updated.');
    }

    public function destroy(UrlRedirect $redirect)
    {
        $redirect->delete();

        return redirect()->route('sitemap.redirects.index')->with('success', 'Redirect deleted.');
    }

    /**
     * Run `php artisan optimize:clear` (config, route, view, event caches).
     */
    public function optimizeClear()
    {
        if (! config('sitemap_automation.redirects.allow_optimize_clear_from_admin', true)) {
            return redirect()->route('sitemap.redirects.index')->with('success', 'Optimize clear is disabled in config.');
        }

        Artisan::call('optimize:clear');

        return redirect()->route('sitemap.redirects.index')->with('success', 'Laravel caches cleared (optimize:clear).');
    }

    /**
     * @return array<string, mixed>
     */
    protected function validated(Request $request): array
    {
        return $request->validate([
            'old_url' => 'required|string|max:2048',
            'new_url' => 'required_unless:status_code,410|nullable|string|max:2048',
            'status_code' => 'required|integer|in:301,302,307,308,410',
            'notes' => 'nullable|string|max:5000',
        ]);
    }

    /**
     * @param  array<string, mixed>  $data
     * @return string|null
     */
    protected function prepareNewUrlFromPayload(array $data)
    {
        if ((int) $data['status_code'] === 410) {
            return null;
        }

        $raw = isset($data['new_url']) ? trim((string) $data['new_url']) : '';

        return $raw === '' ? null : $raw;
    }

    /**
     * @param  string|null  $newUrl
     * @return string|null
     */
    protected function normalizeNewUrl($newUrl)
    {
        if ($newUrl === null || $newUrl === '') {
            return null;
        }

        if (RedirectPathNormalizer::isAbsoluteUrl($newUrl)) {
            return $newUrl;
        }

        return RedirectPathNormalizer::normalizePath($newUrl);
    }
}
