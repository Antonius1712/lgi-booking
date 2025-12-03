<?php

namespace App\Services;

class LgiPassword
{
    protected static function getIv()
    {
        return base64_decode(config('services.lgi.iv_encode'));
    }

    protected static function getKey()
    {
        return base64_decode(config('services.lgi.key_encode'));
    }

    public static function Encrypt(string $password): string
    {
        $iv = self::getIv();
        $key = self::getKey();

        $unicodePassword = mb_convert_encoding($password, 'UTF-16LE', 'UTF-8');
        $text = base64_decode(base64_encode($unicodePassword));

        $block = 16;
        $padding = $block - (strlen($text) % $block);
        $text .= str_repeat(chr($padding), $padding);

        $crypttext = openssl_encrypt($text, 'aes-256-cbc', $key, OPENSSL_RAW_DATA | OPENSSL_ZERO_PADDING, $iv);

        return base64_encode($crypttext);
    }

    public static function Decrypt(string $password): string
    {
        $iv = self::getIv();
        $key = self::getKey();

        $decrypttext = openssl_decrypt(base64_decode($password), 'aes-256-cbc', $key, OPENSSL_RAW_DATA | OPENSSL_ZERO_PADDING, $iv);

        return preg_replace('/[\x00-\x09\x0B\x0C\x0E-\x1F\x7F]/u', '', $decrypttext);
    }
}
