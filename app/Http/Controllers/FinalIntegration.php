<?php

namespace App\Http\Controllers;
use App\Services\MetadataExtractorService;
use App\Services\DomainIntelligenceService;
use App\Services\CompanyLocationService;
use Illuminate\Http\Request;

class FinalIntegration extends Controller
{
    protected $metadataExtractorService;
    protected $domainIntelligenceService;
    protected $companyLocationService;

    public function __construct(
        MetadataExtractorService $metadataExtractorService,
        DomainIntelligenceService $domainIntelligenceService,
        CompanyLocationService $companyLocationService
    ) {
        $this->metadataExtractorService = $metadataExtractorService;
        $this->domainIntelligenceService = $domainIntelligenceService;
        $this->companyLocationService = $companyLocationService;
    }

    public function show(Request $request)
    {
        $validated = $request->validate([
            'domain' => 'required|string',
        ]);

        $domain = $this->normalizeDomain($validated['domain']);
        if (!preg_match('/^([a-z0-9-]+\.)+[a-z]{2,}$/i', $domain)) {
            return response()->json([
                'error' => 'Format domain tidak valid. Contoh: paper.id',
            ], 422);
        }

        $url = "https://{$domain}";

        $website = $this->safeCall(fn () => $this->metadataExtractorService->extractMetadata($url));
        $domainInfo = $this->safeCall(fn () => $this->domainIntelligenceService->lookup($domain));
        $location = $this->safeCall(fn () => $this->companyLocationService->search($domain));

        return response()->json([
            'website' => $website,
            'domain' => $domainInfo,
            'location' => $location,
        ]);
    }

    private function normalizeDomain(string $input): string
    {
        $input = trim($input);

        if (preg_match('#^https?://#i', $input)) {
            $parsed = parse_url($input);
            if (!empty($parsed['host'])) {
                return strtolower($parsed['host']);
            }
            $input = preg_replace('#^https?://#i', '', $input);
        }

        $input = explode('/', $input)[0];
        $input = trim($input, '/');

        return strtolower($input);
    }

    private function safeCall(\Closure $callback): array
    {
        try {
            return $callback();
        } catch (\Throwable $e) {
            return ['error' => $e->getMessage()];
        }
    }
}