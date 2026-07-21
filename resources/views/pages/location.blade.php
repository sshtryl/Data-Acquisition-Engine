@extends('layouts.default')

@section('content')
   <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 pt-34">
         <h1 class="text-2xl font-semibold text-heading text-center md:text-3xl">
        Company Location Finder
    </h1>
    <p class="mt-2 text-center text-sm text-body">
        Analyze company location using OpenStreetMap
    </p>

    <form class="max-w-md mx-auto mt-10" action="{{ route('domain.lookup') }}" data-domain-form>
    <label for="domain" class="block mb-2.5 text-sm font-medium text-heading sr-only">Domain input</label>
    <div class="relative">
        <input type="search" id="domain" name="domain" class="block w-full p-3 bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-base focus:ring-brand focus:border-brand shadow-xs placeholder:text-body" placeholder="Enter domain" required />
        <button type="submit" class="absolute end-1.5 bottom-1.5 text-white bg-brand hover:bg-brand-strong box-border border border-transparent focus:ring-4 focus:ring-brand-medium shadow-xs font-medium leading-5 rounded text-xs px-3 py-1.5 focus:outline-none">Execute</button>
    </div>
</form>

<div class="max-w-2xl mx-auto mt-10">
    <div class="bg-neutral-secondary-medium border border-default-medium rounded-base p-4">
        <h2 class="text-lg font-semibold text-heading mb-2">Enter a domain to find location using OpenStreetMap</h2>
        <p class="text-sm text-body">Here is the domain location from the provided domain:</p>
        <div class="mt-2 space-y-2 text-sm text-body" data-domain-result></div>
    </div>
</div>

@endsection