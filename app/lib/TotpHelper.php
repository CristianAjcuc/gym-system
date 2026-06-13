<?php

class TotpHelper
{
    public static function generateSecret($length = 16)
    {
        $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
        $secret = '';
        for ($i = 0; $i < $length; $i++) {
            $secret .= $chars[random_int(0, strlen($chars) - 1)];
        }
        return $secret;
    }

    public static function getOtpAuthUrl($issuer, $accountName, $secret)
    {
        $issuer = rawurlencode($issuer);
        $accountName = rawurlencode($accountName);

        return "otpauth://totp/{$issuer}:{$accountName}?secret={$secret}&issuer={$issuer}&algorithm=SHA1&digits=6&period=30";
    }

    public static function verifyCode($secret, $code, $discrepancy = 0, $currentTimeSlice = null)
    {
        if ($currentTimeSlice === null) {
            $currentTimeSlice = floor(time() / 30);
        }

        $code = trim($code);

        if (!preg_match('/^\d{6}$/', $code)) {
            return false;
        }

        for ($i = -$discrepancy; $i <= $discrepancy; $i++) {
            $calculatedCode = self::getCode($secret, $currentTimeSlice + $i);
            if (hash_equals($calculatedCode, $code)) {
                return true;
            }
        }

        return false;
    }

    public static function getCode($secret, $timeSlice = null)
    {
        if ($timeSlice === null) {
            $timeSlice = floor(time() / 30);
        }

        $secretKey = self::base32Decode($secret);

        if ($secretKey === false) {
            return '';
        }

        $time = pack('N*', 0) . pack('N*', $timeSlice);
        $hm = hash_hmac('SHA1', $time, $secretKey, true);
        $offset = ord(substr($hm, -1)) & 0x0F;
        $hashPart = substr($hm, $offset, 4);

        $value = unpack('N', $hashPart)[1] & 0x7FFFFFFF;
        $modulo = 10 ** 6;

        return str_pad($value % $modulo, 6, '0', STR_PAD_LEFT);
    }

    private static function base32Decode($secret)
    {
        if (empty($secret)) {
            return false;
        }

        $base32chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
        $base32charsFlipped = array_flip(str_split($base32chars));

        $paddingCharCount = substr_count($secret, '=');
        $allowedValues = [6, 4, 3, 1, 0];

        if (!in_array($paddingCharCount, $allowedValues, true)) {
            return false;
        }

        $secret = str_replace('=', '', strtoupper($secret));
        $binaryString = '';

        for ($i = 0; $i < strlen($secret); $i++) {
            if (!isset($base32charsFlipped[$secret[$i]])) {
                return false;
            }
            $binaryString .= str_pad(decbin($base32charsFlipped[$secret[$i]]), 5, '0', STR_PAD_LEFT);
        }

        $eightBits = str_split($binaryString, 8);
        $decoded = '';

        foreach ($eightBits as $bits) {
            if (strlen($bits) === 8) {
                $decoded .= chr(bindec($bits));
            }
        }

        return $decoded;
    }
}