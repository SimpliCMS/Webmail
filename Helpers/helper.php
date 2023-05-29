<?php

use Illuminate\Support\Facades\Cache;
use Laravolt\Avatar\Facade as Avatar;
use Illuminate\Support\Facades\Http;

function getLogo($fromEmail, $fromName) {
    $emailParts = explode('@', $fromEmail);
    $domain = isset($emailParts[1]) ? $emailParts[1] : null;

    // Extract the base domain from the email domain
    $baseDomain = getBaseDomain($domain);

    // Load known providers from file
    $knownProvidersFile = module_path('Webmail').'/resources/assets/known_providers.txt';
    $knownProviders = file($knownProvidersFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

    // Check if the base domain is a known email provider
    if (in_array($baseDomain, $knownProviders)) {
        // Skip BIMI and other lookups, directly generate default avatar/logo
        return generateDefaultAvatar($fromEmail, $fromName);
    }

    // BIMI Lookup
    $bimiRecords = Cache::remember('bimi_records_' . $baseDomain, 3600, function () use ($baseDomain) {
        return dns_get_record('default._bimi.' . $baseDomain, DNS_TXT);
    });

    foreach ($bimiRecords as $record) {
        if (isset($record['txt']) && strpos($record['txt'], 'v=BIMI1') !== false) {
            $txtParts = explode(';', $record['txt']);

            foreach ($txtParts as $part) {
                $part = trim($part);

                if (strpos($part, 'l=') === 0) {
                    $logoUrl = trim(substr($part, 2));

                    // Fetch the logo and return it
                    $logoResponse = Http::get($logoUrl);

                    if ($logoResponse->ok()) {
                        return $logoResponse->body();
                    }
                }
            }
        }
    }

    // BIMI logo not found, try Clearbit logo API
    $clearbitLogoUrl = 'https://logo.clearbit.com/' . $baseDomain . '?size=200&format=jpg';
    $logoResponse = Http::get($clearbitLogoUrl);

    if ($logoResponse->ok()) {
        return $clearbitLogoUrl;
    }

    // No Clearbit logo found, generate a default avatar/logo
    return generateDefaultAvatar($fromEmail, $fromName);
}




function getBaseDomain($domain) {
    $parts = explode('.', $domain);
    $numParts = count($parts);

    // Check if the domain has at least two parts
    if ($numParts >= 2) {
        // Get the last two parts of the domain
        $baseDomain = $parts[$numParts - 2] . '.' . $parts[$numParts - 1];
        return $baseDomain;
    }

    // Return the original domain if it cannot be stripped down
    return $domain;
}

function generateDefaultAvatar($fromEmail, $fromName) {
    $gravatarUrl = 'https://www.gravatar.com/avatar/' . md5(strtolower(trim($fromEmail))) . '?s=200&d=404';

    $response = Http::head($gravatarUrl);

    if ($response->status() === 404) {
        $avatar = Avatar::create($fromName)->setFontSize(72)->setDimension(200, 200)->toBase64();
        return $avatar;
    } else {
        return $gravatarUrl;
    }
}
