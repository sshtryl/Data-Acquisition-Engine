<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MetadataController;
use App\Http\Controllers\DomainController;

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