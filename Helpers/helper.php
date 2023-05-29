<?php

use Illuminate\Support\Facades\Cache;
use Laravolt\Avatar\Facade as Avatar;
use Illuminate\Support\Facades\Http;

function getBimiLogo($fromEmail, $fromName) {
    $emailParts = explode('@', $fromEmail);
    $domain = isset($emailParts[1]) ? $emailParts[1] : null;
    
    $bimiRecords = Cache::remember('bimi_records_' . $domain, 3600, function () use ($domain) {
                return dns_get_record('default._bimi.' . $domain, DNS_TXT);
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

    // BIMI logo not found, generate a default avatar/logo using the Gravatar service
    return generateDefaultAvatar($fromEmail,$fromName);
}

function generateDefaultAvatar($fromEmail, $fromName)
{
    $gravatarUrl = 'https://www.gravatar.com/avatar/' . md5(strtolower(trim($fromEmail))) . '?s=200&d=404';

    $response = Http::head($gravatarUrl);

    if ($response->status() === 404) {
        $avatar = Avatar::create($fromName)->setFontSize(72)->setDimension(200, 200)->toBase64();
        return $avatar;
    } else {
        return $gravatarUrl;
    }
}
