<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use DOMDocument;
use DOMXPath;

class MetadataExtractorService
{
    public function extractMetadata(string $url): array
    {
        $response = Http::get($url);

        if ($response->failed()) {
            throw new \Exception("Failed to fetch the URL: $url");
        }

        $htmlContent = $response->body();

        libxml_use_internal_errors(true);
        $dom = new DOMDocument();
        $dom->loadHTML($htmlContent);
        libxml_clear_errors();

        $xpath = new DOMXPath($dom);

        $urlNode = $xpath->query('//link[@rel="canonical"]/@href')->item(0);
        $canonicalUrl = $urlNode ? $urlNode->nodeValue : null;

        // Extract title
        $titleNode = $xpath->query('//title')->item(0);
        $title = $titleNode ? $titleNode->textContent : null;

        // Extract meta description
        $descriptionNode = $xpath->query('//meta[@name="description"]/@content')->item(0);
        $description = $descriptionNode ? $descriptionNode->nodeValue : null;

        $canonical = $canonicalUrl ?? $url;

        $faviconNode = $xpath->query('//link[@rel="icon"]/@href')->item(0);



        $emailNodes = $xpath->query('//a[contains(@href, "mailto:")]/@href');
        $emails = [];
        foreach ($emailNodes as $emailNode) {
            $emails[] = str_replace('mailto:', '', $emailNode->nodeValue);
        }

        $phoneNodes = $xpath->query('//a[contains(@href, "tel:")]/@href');
        $phones = [];
        foreach ($phoneNodes as $phoneNode) {
            $phones[] = str_replace('tel:', '', $phoneNode->nodeValue);
        }

        $socialMediaNodes = $xpath->query('//a[contains(@href, "facebook.com") or contains(@href, "twitter.com") or contains(@href, "linkedin.com") or contains(@href, "instagram.com")]/@href');
        $socialMediaLinks = [];
        foreach ($socialMediaNodes as $socialMediaNode) {
            $socialMediaLinks[] = $socialMediaNode->nodeValue;
        }

        $ogTitleNode = $xpath->query('//meta[@property="og:title"]/@content')->item(0);
        $ogDescriptionNode = $xpath->query('//meta[@property="og:description"]/@content')->item(0);
        $ogImageNode = $xpath->query('//meta[@property="og:image"]/@content')->item(0);

        $openGraph = (object) [
        'title' => $ogTitleNode ? $ogTitleNode->nodeValue : null,
        'description' => $ogDescriptionNode ? $ogDescriptionNode->nodeValue : null,
        'image' => $ogImageNode ? $ogImageNode->nodeValue : null,
        ];

        return [
            'url' => $canonicalUrl,
            'title' => $title,
            'description' => $description,
            'canonical' => $canonical,
            'favicon' => $faviconNode ? $faviconNode->nodeValue : null,
            'email' => $emails,
            'phone' => $phones,
            'social_media' => $socialMediaLinks,
            'open_graph' => $openGraph,
        ];
    }
}