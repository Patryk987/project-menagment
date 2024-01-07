<?php

namespace RSA;

class RsaKeyModel
{
    private string $public_key;
    private ?string $private_key = null;

    public function __construct($public_key, $private_key = null)
    {
        $this->public_key = $public_key;
        if ($private_key != null)
            $this->private_key = $private_key;
    }

    // Geter

    public function get_public_key(): string
    {
        return $this->public_key;
    }

    public function get_private_key(): string
    {
        return $this->private_key;
    }

    // Seter

    public function set_public_key($public_key): void
    {
        $this->public_key = $public_key;
    }

    public function set_private_key($private_key): void
    {
        $this->private_key = $private_key;
    }


}