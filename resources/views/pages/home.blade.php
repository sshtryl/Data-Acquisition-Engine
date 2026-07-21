@extends('layouts.default')

@section('content')
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 pt-34">
         <h1 class="text-2xl font-semibold text-heading text-center md:text-3xl">
        Metadata Extractor
    </h1>
    <p class="mt-2 text-center text-sm text-body">
        Extract metadata from any URL 🌐
    </p>
<form class="max-w-md mx-auto mt-10">   
    <label for="search" class="block mb-2.5 text-sm font-medium text-heading sr-only ">URL input</label>
    <div class="relative">
        <input type="search" id="search" class="block w-full p-3 bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-base focus:ring-brand focus:border-brand shadow-xs placeholder:text-body" placeholder="Enter URL" required />
        <button type="button" class="absolute end-1.5 bottom-1.5 text-white bg-brand hover:bg-brand-strong box-border border border-transparent focus:ring-4 focus:ring-brand-medium shadow-xs font-medium leading-5 rounded text-xs px-3 py-1.5 focus:outline-none">Execute</button>
    </div>
</form>

{{-- Result --}}
<div class="max-w-2xl mx-auto mt-10">
    <div class="bg-neutral-secondary-medium border border-default-medium rounded-base p-4">
        <h2 class="text-lg font-semibold text-heading mb-2">Enter a URL to extract TITLE and META information.</h2>
        <p class="text-sm text-body">Here is the extracted metadata from the provided URL:</p>
        <pre class="mt-2 bg-neutral-primary-medium p-4 rounded-base overflow-x-auto text-sm text-body"></pre>
    </div>
</div>
</div>
@endsection