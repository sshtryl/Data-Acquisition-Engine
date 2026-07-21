<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MetadataController;

Route::get('/', function () {
    return view('pages.home');
});

Route::get('/metadata-extractor', [MetadataController::class, 'index'])->name('metadata.index');
Route::match(['get', 'post'], '/metadata-extractor/extract', [MetadataController::class, 'extract'])->name('metadata.extract');