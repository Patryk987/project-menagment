<?php
namespace RSA;

class EncryptDecryptRSA
{

    private RsaKeyModel $keys;

    public function __construct($id, $prefix = "")
    {
        $key_management = new RSAKeyManagement($id, $prefix);
        $this->keys = $key_management->get_keys();
    }

    public function encrypt($data)
    {
        openssl_public_encrypt($data, $encrypted, $this->keys->get_public_key());
        $encrypted = base64_encode($encrypted);
        return $encrypted;
    }

    public function decrypt($encrypted)
    {
        $encrypted = base64_decode($encrypted);
        openssl_private_decrypt($encrypted, $decrypted, $this->keys->get_private_key());
        return $decrypted;
    }
}