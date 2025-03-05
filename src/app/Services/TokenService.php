<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Str;

class TokenService
{
    public static function createToken(array $payload, string $secret, string $algorithm = 'HS256'): string
    {
        $header = json_encode(['typ' => 'JWT', 'alg' => $algorithm]);
        $base64Header = base64_encode($header);
        $base64Payload = base64_encode(json_encode($payload));
        $signature = hash_hmac('sha256', "$base64Header.$base64Payload", $secret, true);
        $base64Signature = base64_encode($signature);

        return "$base64Header.$base64Payload.$base64Signature";
    }

    public static function decodeToken(string $token, string $secret): array
    {
        $parts = explode('.', $token);

        if (count($parts) !== 3) {
            throw new Exception('Invalid token structure');
        }

        [$base64Header, $base64Payload, $base64Signature] = $parts;

        $header = json_decode(base64_decode($base64Header), true);
        $payload = json_decode(base64_decode($base64Payload), true);
        $signature = base64_decode($base64Signature);

        $validSignature = hash_hmac('sha256', "$base64Header.$base64Payload", $secret, true);

        if ($signature !== $validSignature) {
            throw new Exception('Invalid token signature');
        }

        return $payload;
    }
}