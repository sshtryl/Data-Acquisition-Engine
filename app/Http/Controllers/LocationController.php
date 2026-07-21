<?php

namespace App\Http\Controllers;

use App\Services\CompanyLocationService;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    protected $companyLocationService;

    public function __construct(CompanyLocationService $companyLocationService)
    {
        $this->companyLocationService = $companyLocationService;
    }

    public function index()
    {
        return view('pages.location');
    }

    public function search(Request $request)
    {
        try {
            $validated = $request->validate([
                'query' => 'required|string|min:2',
            ]);

            $result = $this->companyLocationService->search($validated['query']);
            return response()->json($result);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'error' => $e->errors()['query'][0] ?? 'Query tidak valid.',
            ], 422);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
}