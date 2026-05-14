<?php

namespace MightyWarnersKochi\SitemapKit\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use MightyWarnersKochi\SitemapKit\Services\MissingUrlLogRecorder;

/**
 * Post-response missing-URL logging: runs after $next($request), then inspects status/body.
 */
class RecordMissingUrls
{
    /** @var MissingUrlLogRecorder */
    protected $recorder;

    public function __construct(MissingUrlLogRecorder $recorder)
    {
        $this->recorder = $recorder;
    }

    /**
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);
        $this->recorder->record($request, $response);

        return $response;
    }
}
