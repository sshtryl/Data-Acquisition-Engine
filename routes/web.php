<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MetadataExtractorController;
use App\Http\Controllers\DomainIntelligenceController;
use App\Http\Controllers\CompanyLocationController;
use App\Http\Controllers\FinalIntegration;


Route::get('/', function () {
    return view('pages.home');
});

Route::get('/domain-intelligence', function () {
    return view('pages.domainIntelligence');
});

Route::get('/company-intelligence', function () {
    return view('pages.location');
});

Route::get('/final-integration', function () {
    return view('pages.finalIntegration');
});

Route::post('/extract/website', [MetadataExtractorController::class, 'extract'])->name('metadata.extract');
Route::post('/extract/domain', [DomainIntelligenceController::class, 'lookup'])->name('domain.lookup');
Route::post('/extract/location', [CompanyLocationController::class, 'search'])->name('location.search');
Route::get('/company-information', [FinalIntegration::class, 'show'])->name('company.information');