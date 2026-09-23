<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CompressResponse
{
    /**
     * Handle an incoming request and compress the HTTP response if supported.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Do not compress binary file downloads or streamed responses
        if ($response instanceof BinaryFileResponse || $response instanceof StreamedResponse) {
            return $response;
        }

        // Check if compression is supported by the client and extension is loaded
        if (!extension_loaded('zlib') || !$request->headers->has('Accept-Encoding')) {
            return $response;
        }

        $acceptEncoding = (string) $request->header('Accept-Encoding');
        if (!str_contains($acceptEncoding, 'gzip')) {
            return $response;
        }

        // Do not compress if already encoded
        if ($response->headers->has('Content-Encoding')) {
            return $response;
        }

        $content = $response->getContent();
        if ($content === false || strlen($content) < 1024) {
            // Skip tiny responses (< 1KB) as compression overhead isn't worth it
            return $response;
        }

        $contentType = (string) $response->headers->get('Content-Type');
        $isCompressible = str_contains($contentType, 'text/')
            || str_contains($contentType, 'application/json')
            || str_contains($contentType, 'application/javascript')
            || str_contains($contentType, 'image/svg+xml')
            || empty($contentType);

        if (!$isCompressible) {
            return $response;
        }

        $compressed = gzencode($content, 6);
        if ($compressed !== false && strlen($compressed) < strlen($content)) {
            $response->setContent($compressed);
            $response->headers->set('Content-Encoding', 'gzip');
            $response->headers->set('Vary', 'Accept-Encoding');
            $response->headers->set('Content-Length', (string) strlen($compressed));
        }

        return $response;
    }
}
