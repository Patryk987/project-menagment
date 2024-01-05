<?php
namespace RSA;

interface KeyManagementInterface
{

}

class RSAKeyManagement
{
    private int $key_size = 2048;
    private $id;
    private $prefix;
    private string $symmetric_algorithm_password;

    public function __construct($id, $prefix = "")
    {
        $this->id = $id;
        $this->prefix = $prefix;
    }

    public function set_key_size(int $size)
    {
        $this->key_size = $size;
    }

    public function generate_key()
    {
        // Generowanie pary kluczy RSA
        $private_key_resource = openssl_pkey_new(
            array(
                'private_key_bits' => $this->key_size,
                'private_key_type' => OPENSSL_KEYTYPE_RSA,
            )
        );

        // Ekstrakcja klucza prywatnego
        openssl_pkey_export($private_key_resource, $private_key);

        // Ekstrakcja klucza publicznego
        $public_key_details = openssl_pkey_get_details($private_key_resource);
        $public_key = $public_key_details['key'];

        $this->save_private_key($private_key);
        $this->save_public_key($public_key);
    }

    public function get_keys(): RsaKeyModel
    {
        $private_key = file_get_contents(__DIR__ . '/keys/' . $this->id . '_private_key.pem');
        $public_key = file_get_contents(__DIR__ . '/keys/' . $this->id . '_public_key.pem');

        return new RsaKeyModel($public_key, $private_key);
    }

    private function save_private_key($private_key)
    {

        file_put_contents(__DIR__ . '/keys/' . $this->prefix . $this->id . '_private_key.pem', $private_key);

    }

    private function save_public_key($public_key)
    {

        file_put_contents(__DIR__ . '/keys/' . $this->prefix . $this->id . '_public_key.pem', $public_key);

    }
}
