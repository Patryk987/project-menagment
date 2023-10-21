<?php

namespace ModuleManager;

/*
 * Class to encrypt and decrypt string
 */
class EncryptData
{

    private static string $ciphering = "AES-128-CTR";
    private static int $options = 0;
    private static string $iv = '294721420234393W';
    protected static $password_protected_method = PASSWORD_DEFAULT;

    public static function encrypt($text, $temp_key = NULL)
    {
        if (!is_null($temp_key))
            $local_key = $temp_key;
        else
            $local_key = $_ENV["SECRET"];

        $iv_length = openssl_cipher_iv_length(static::$ciphering);
        $encrypt_text = openssl_encrypt($text, static::$ciphering, $local_key, static::$options, static::$iv);

        return $encrypt_text;
    }

    public static function decrypt($text, $temp_key = NULL)
    {
        if (!is_null($temp_key))
            $local_key = $temp_key;
        else
            $local_key = $_ENV["SECRET"];

        $iv_length = openssl_cipher_iv_length(static::$ciphering);
        $decrypt_text = openssl_decrypt($text, static::$ciphering, $local_key, static::$options, static::$iv);

        return $decrypt_text;
    }

    public static function password_hash($password)
    {
        $password_hash = password_hash($password, static::$password_protected_method);

        return $password_hash;
    }

    public static function password_hash_verify($password, $hash)
    {
        $status = password_verify($password, $hash);
        return $status;
    }

}