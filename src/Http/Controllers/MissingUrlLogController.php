<?php

namespace MightyWarnersKochi\SitemapKit\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use MightyWarnersKochi\SitemapKit\Models\MissingUrlLog;

class MissingUrlLogController extends Controller
{
    public function index(Request $request)
    {
        $query = MissingUrlLog::query()->orderByDesc('last_seen_at');

        if ($search = trim((string) $request->get('q'))) {
            $query->where('url', 'like', '%'.$search.'%');
        }

        $logs = $query->paginate(25)->withQueryString();

        return view('sitemap-automation::missing-urls.index', compact('logs'));
    }

    public function promote(MissingUrlLog $missingUrlLog)
    {
        return redirect()->route('sitemap.redirects.create', [
            'old_url' => $missingUrlLog->url,
        ]);
    }

    public function destroy(MissingUrlLog $missingUrlLog)
    {
        $missingUrlLog->delete();

        return redirect()->route('sitemap.missing-urls.index')->with('success', 'Log entry removed.');
    }
}
