<?php

namespace App\Http\Controllers;

use App\Services\MetadataExtractorService;
use Illuminate\Http\Request;

class MetadataController extends Controller
{
    protected $metadataExtractorService;

    public function __construct(MetadataExtractorService $metadataExtractorService)
    {
        $this->metadataExtractorService = $metadataExtractorService;
    }

    public function extract(Request $request)
    {
        $request->validate([
            'url' => 'required|url',
        ]);

        try {
            $metadata = $this->metadataExtractorService->extractMetadata($request->input('url'));
            return response()->json($metadata);
        } catch (\Illuminate\Validation\ValidationException $e) {
        return response()->json([
            'error' => $e->errors()['url'][0] ?? 'URL tidak valid.',
        ], 422);
        }
        catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()], 400);
        }
    }
}