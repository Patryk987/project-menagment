<?php

namespace AES;

class EncryptDecryptAES
{
    private string $key;
    private string $ciphering = "AES-128-CTR";
    private int $options = 0;
    private ?string $iv = null;

    public function __construct(string $key, $options = 0)
    {
        $this->key = $key;
        $this->options = $options;
    }

    public function set_iv($iv)
    {
        $this->iv = $iv;
    }

    public function create_iv()
    {
        $iv_length = openssl_cipher_iv_length($this->ciphering);
        $iv = openssl_random_pseudo_bytes($iv_length);

        return base64_encode($iv);
    }

    public function encrypt($text)
    {
        if (is_null($this->iv)) {
            $iv = $this->create_iv();
            $iv = base64_decode($iv);
        } else {
            $iv = $this->iv;
        }

        $encrypt_text = openssl_encrypt(
            $text,
            $this->ciphering,
            $this->key,
            $this->options,
            $iv
        );

        $encrypt_text = base64_encode($iv . $encrypt_text);
        return $encrypt_text;

    }

    public function decrypt($text)
    {

        $c = base64_decode($text);
        $iv_length = openssl_cipher_iv_length($this->ciphering);
        $iv = substr($c, 0, $iv_length);

        $text_raw = substr($c, $iv_length);

        $decrypt_text = openssl_decrypt(
            $text_raw,
            $this->ciphering,
            $this->key,
            $this->options,
            $iv
        );

        return $decrypt_text;

    }

}