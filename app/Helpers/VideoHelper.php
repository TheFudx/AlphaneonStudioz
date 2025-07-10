<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Crypt;
use Exception;
use Illuminate\Support\Str; 
use Vinkla\Hashids\Facades\Hashids;
class VideoHelper
{
    
    
    // private static ?string $encryptionKey = null;

    // public static function setKey(string $key): void
    // {
    //     if (strlen($key) !== 16) {
    //         throw new Exception("Video encryption key must be exactly 16 bytes long for AES-128-CTR.");
    //     }
    //     self::$encryptionKey = $key;
    // }

   
    // public static function generateObfuscatedID($id = null): string
    // {
    //     return Str::random(6);
    // }

    // public static function encryptID($id): string
    // {
    //     if (is_null(self::$encryptionKey)) {
    //         throw new Exception("Encryption key not set for VideoHelper. Call VideoHelper::setKey() first.");
    //     }

    //     $ciphering = "AES-128-CTR";
    //     $iv_length = openssl_cipher_iv_length($ciphering);
    //     $options = 0;

    //     $encryption_iv = openssl_random_pseudo_bytes($iv_length);
    //     $encryption_key = self::$encryptionKey;

    //     $encrypted = openssl_encrypt(
    //         (string)$id,
    //         $ciphering,
    //         $encryption_key,
    //         $options,
    //         $encryption_iv
    //     );

    //     if ($encrypted === false) {
    //         throw new Exception("Video ID encryption failed.");
    //     }

    //     $combined = $encryption_iv . $encrypted;
    //     $url_safe_encoded = strtr(base64_encode($combined), '+/', '-_');
    //     $url_safe_encoded = rtrim($url_safe_encoded, '=');

    //     return $url_safe_encoded;
    // }
    // public static function decryptID(string $encryptedId): string|false  
    // {
    //     if (is_null(self::$encryptionKey)) {
    //         throw new Exception("Encryption key not set for VideoHelper. Call VideoHelper::setKey() first.");
    //     }

    //     $ciphering = "AES-128-CTR";
    //     $iv_length = openssl_cipher_iv_length($ciphering);
    //     $options = 0;

    //     $standard_base64 = strtr($encryptedId, '-_', '+/');
    //     $padding_needed = strlen($standard_base64) % 4;
    //     if ($padding_needed) {
    //         $standard_base64 .= str_repeat('=', 4 - $padding_needed);
    //     }

    //     $decoded = base64_decode($standard_base64);
    //     if ($decoded === false || strlen($decoded) < $iv_length) {
    //         return false;
    //     }

    //     $decryption_iv = substr($decoded, 0, $iv_length);
    //     $encrypted_data_only = substr($decoded, $iv_length);

    //     $decryption_key = self::$encryptionKey;

    //     $decrypted = openssl_decrypt(
    //         $encrypted_data_only,
    //         $ciphering,
    //         $decryption_key,
    //         $options,
    //         $decryption_iv
    //     );

    //     return $decrypted;
    // }
     public static function encryptID(int $id): string
    {
        return Hashids::encode($id); // Outputs 6+ character obfuscated string
    }

    public static function decryptID(string $obfuscated): ?int
    {
        $decoded = Hashids::decode($obfuscated);
        return $decoded[0] ?? null;
    }
}