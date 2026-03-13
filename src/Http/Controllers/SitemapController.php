<?php

namespace Dev1kochiCrypto\SitemapKit\Http\Controllers;

use Illuminate\Routing\Controller;
use Dev1kochiCrypto\SitemapKit\Services\SitemapService;
use Dev1kochiCrypto\SitemapKit\Jobs\UpdateSitemapJob;
use Illuminate\Http\Request;

class SitemapController extends Controller
{
    protected $sitemapService;

    public function __construct(SitemapService $sitemapService)
    {
        $this->sitemapService = $sitemapService;
    }

    public function index()
    {
        $exists = file_exists(public_path('sitemap.xml'));
        return view('sitemap-automation::sitemap', compact('exists'));
    }


    public function generate(Request $request)
    {
        dispatch(new UpdateSitemapJob());

        return redirect()->back()->with('success', 'Sitemap regeneration has been queued.');
    }

    public function edit()
    {
        $path = public_path('sitemap.xml');
        $content = file_exists($path) ? file_get_contents($path) : '';
        return view('sitemap-automation::edit', compact('content'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'content' => 'required|string',
        ]);

        $path = public_path('sitemap.xml');
        file_put_contents($path, $request->input('content'));

        return redirect()->route('sitemap.index')->with('success', 'Sitemap.xml updated successfully.');
    }
}
