<?php

namespace App\Http\Controllers;

use App\Models\PropertySetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Exception;

class MapController extends Controller
{
    /**
     * Get map settings for frontend rendering (without exposing API key).
     * This endpoint returns only the embed URL and other map-related settings.
     * The API key is never exposed in this response.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getMapSettings()
    {
        $embedUrl = PropertySetting::where('key', 'google_maps_embed_url')->value('value');

        return response()->json([
            'data' => [
                'embed_url' => $embedUrl,
            ],
            'message' => 'Map settings fetched successfully'
        ], 200);
    }

    /**
     * Proxy Google Static Maps API request server-side.
     * This method uses the stored API key on the server to fetch static map images,
     * ensuring the API key is never exposed to the browser.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function staticMap(Request $request)
    {
        $request->validate([
            'center' => 'nullable|string',
            'zoom' => 'nullable|integer|min:1|max:21',
            'size' => 'nullable|string',
            'style' => 'nullable|string',
            'markers' => 'nullable|string',
            'path' => 'nullable|string',
        ]);

        try {
            $apiKey = PropertySetting::where('key', 'google_maps_api_key')->value('value');

            if (!$apiKey || $apiKey === 'YOUR_GOOGLE_MAPS_API_KEY_HERE') {
                return response()->json([
                    'error' => 'Google Maps API key is not configured'
                ], 503);
            }

            $params = [
                'key' => $apiKey,
            ];

            // Add optional parameters if provided
            if ($request->has('center')) {
                $params['center'] = $request->input('center');
            }

            if ($request->has('zoom')) {
                $params['zoom'] = $request->input('zoom');
            }

            if ($request->has('size')) {
                $params['size'] = $request->input('size');
            } else {
                $params['size'] = '600x400'; // Default size
            }

            if ($request->has('style')) {
                $params['style'] = $request->input('style');
            }

            if ($request->has('markers')) {
                $params['markers'] = $request->input('markers');
            }

            if ($request->has('path')) {
                $params['path'] = $request->input('path');
            }

            $response = Http::get('https://maps.googleapis.com/maps/api/staticmap', $params);

            if ($response->failed()) {
                return response()->json([
                    'error' => 'Failed to fetch map from Google Maps API'
                ], $response->status());
            }

            // Return the image with appropriate headers
            return response($response->body())
                ->header('Content-Type', 'image/png')
                ->header('Cache-Control', 'public, max-age=3600');
        } catch (Exception $e) {
            return response()->json([
                'error' => 'An error occurred while fetching the map',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
