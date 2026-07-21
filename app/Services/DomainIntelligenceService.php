<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class DomainIntelligenceService
{
    public function lookup(string $domain): array
    {
        $response = Http::timeout(10)->get("https://rdap.org/domain/{$domain}");

        if ($response->failed()) {
            throw new \Exception("Domain tidak ditemukan atau RDAP tidak tersedia untuk: {$domain}");
        }

        $data = $response->json();

        return [
            'domain' => $data['ldhName'] ?? $domain,
            'registrar' => $this->extractRegistrar($data),
            'registered_at' => $this->extractEventDate($data, 'registration'),
            'expired_at' => $this->extractEventDate($data, 'expiration'),
            'last_updated' => $this->extractEventDate($data, 'last changed'),
            'status' => $data['status'] ?? [],
            'nameservers' => $this->extractNameservers($data),
        ];
    }

    private function extractRegistrar(array $data): ?string
    {
        foreach ($data['entities'] ?? [] as $entity) {
            if (in_array('registrar', $entity['roles'] ?? [])) {
                foreach ($entity['vcardArray'][1] ?? [] as $field) {
                    if ($field[0] === 'fn') {
                        return $field[3];
                    }
                }
            }
        }
        return null;
    }

    private function extractEventDate(array $data, string $action): ?string
    {
        foreach ($data['events'] ?? [] as $event) {
            if (($event['eventAction'] ?? null) === $action) {
                return $event['eventDate'] ?? null;
            }
        }
        return null;
    }

    private function extractNameservers(array $data): array
    {
        $nameservers = [];
        foreach ($data['nameservers'] ?? [] as $ns) {
            $nameservers[] = $ns['ldhName'] ?? null;
        }
        return array_filter($nameservers);
    }
}