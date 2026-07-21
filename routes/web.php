<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MetadataController;
use App\Http\Controllers\DomainController;
use App\Http\Controllers\LocationController;
use Symfony\Component\Routing\Loader\Configurator\Routes;
use App\Http\Controllers\FinalIntegration;

Route::get('/', function () {
    return view('pages.home');
});


Route::get('/metadata-extractor', [MetadataController::class, 'index'])->name('metadata.index');
Route::match(['get', 'post'], '/metadata-extractor/extract', [MetadataController::class, 'extract'])->name('metadata.extract');

Route::get('/domain-intelligence', function () {
    return view('pages.domainintelligence');
});

Route::get('/domain-intelligence', [DomainController::class, 'index'])->name('domain.index');
Route::post('/domain-intelligence/lookup', [DomainController::class, 'lookup'])->name('domain.lookup');

Route::get('/company-intelligence', function () {
    return view('pages.location');
});

Route::get('/company-intelligence', [LocationController::class, 'index'])->name('location.index');
Route::post('/company-intelligence/search', [LocationController::class, 'search'])->name('location.search');

Route::get('/final-integration', function () {
    return view('pages.finalintegration');
});
Route::get('/company-information', [FinalIntegration::class, 'show'])->name('company.information');