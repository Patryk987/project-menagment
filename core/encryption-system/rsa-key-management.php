<?php
namespace RSA;


class RSAKeyManagement
{
    private int $key_size = 2048;
    protected string $url;
    private \AES\EncryptDecryptAES $aes;
    private string $symmetric_algorithm_password;

    protected string $public_key_destination, $private_key_destination;

    public function __construct($id, $prefix = "", $access_key = null)
    {
        $url = __DIR__ . '/keys/';
        $this->public_key_destination = $url . $prefix . $id . '_public_key.pem';
        $this->private_key_destination = $url . $prefix . $id . '_private_key.pem';

        if ($access_key == null) {
            $this->aes = new \AES\EncryptDecryptAES($_ENV["SECRET"]);
        } else {
            $this->aes = new \AES\EncryptDecryptAES($access_key);

        }
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

        $this->create_keys_if_not_exist();

        // private
        $private_key = file_get_contents($this->private_key_destination);
        $private_key = $this->aes->decrypt($private_key);

        // public
        $public_key = file_get_contents($this->public_key_destination);

        return new RsaKeyModel($public_key, $private_key);
    }

    public function get_public_keys(): RsaKeyModel
    {

        $this->create_keys_if_not_exist();
        $public_key = file_get_contents($this->public_key_destination);

        return new RsaKeyModel($public_key);
    }

    private function save_private_key($private_key)
    {

        $private_key = $this->aes->encrypt($private_key);
        file_put_contents($this->private_key_destination, $private_key);

    }

    private function save_public_key($public_key)
    {

        file_put_contents($this->public_key_destination, $public_key);

    }

    private function create_keys_if_not_exist()
    {
        if (!file_exists($this->public_key_destination)) {
            $this->generate_key();
        }
    }
}
