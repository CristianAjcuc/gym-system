<?php

class CryptoHelper
{
    private const CIPHER = 'AES-256-CBC';

    private static function getKey()
    {
        // Cambia esta clave por una más fuerte y guárdala fuera del código si luego publicas
        return hash('sha256', 'GYM_SYSTEM_2FA_CLAVE_SUPER_SECRETA_2026', true);
    }

    private static function getIv()
    {
        // 16 bytes para AES-256-CBC
        return substr(hash('sha256', 'GYM_SYSTEM_2FA_IV_2026', true), 0, 16);
    }

    public static function encrypt($plainText)
    {
        if ($plainText === null || $plainText === '') {
            return null;
        }

        $encrypted = openssl_encrypt(
            $plainText,
            self::CIPHER,
            self::getKey(),
            0,
            self::getIv()
        );

        return $encrypted !== false ? $encrypted : null;
    }

    public static function decrypt($cipherText)
    {
        if ($cipherText === null || $cipherText === '') {
            return null;
        }

        $decrypted = openssl_decrypt(
            $cipherText,
            self::CIPHER,
            self::getKey(),
            0,
            self::getIv()
        );

        return $decrypted !== false ? $decrypted : null;
    }
}