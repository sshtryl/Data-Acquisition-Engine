<?php

namespace App\Http\Controllers;

use App\Services\DomainIntelligenceService;
use Illuminate\Http\Request;

class DomainController extends Controller
{
    protected $domainIntelligenceService;

    public function __construct(DomainIntelligenceService $domainIntelligenceService)
    {
        $this->domainIntelligenceService = $domainIntelligenceService;
    }

    public function index()
    {
        return view('pages.domainintelligence');
    }

    public function lookup(Request $request)
    {
        try {
            $validated = $request->validate([
                'domain' => ['required', 'regex:/^([a-z0-9-]+\.)+[a-z]{2,}$/i'],
            ]);

            $result = $this->domainIntelligenceService->lookup($validated['domain']);
            return response()->json($result);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'error' => $e->errors()['domain'][0] ?? 'Domain tidak valid.',
            ], 422);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
}