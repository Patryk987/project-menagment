<?php

namespace AES;

class EncryptDecryptFile
{
    private int $encrypt_blocks = 10000;

    public function encrypt_file($source, $key, $dest)
    {

        $key = substr(sha1($key, true), 0, 16);
        $iv = openssl_random_pseudo_bytes(16);

        $error = false;

        if ($fpOut = fopen($dest, 'w')) {

            fwrite($fpOut, $iv);

            if ($fpIn = fopen($source, 'rb')) {

                while (!feof($fpIn)) {

                    $plaintext = fread(
                        $fpIn,
                        16 * $this->encrypt_blocks
                    );

                    $ciphertext = openssl_encrypt(
                        $plaintext,
                        'aes-256-cbc',
                        $key,
                        OPENSSL_RAW_DATA,
                        $iv
                    );

                    $iv = substr($ciphertext, 0, 16);
                    fwrite($fpOut, $ciphertext);

                }

                fclose($fpIn);

            } else {

                $error = true;

            }
            fclose($fpOut);
        } else {
            $error = true;
        }

        return $error ? false : $dest;
    }


    public function decryptFile($source, $key, $dest)
    {
        $key = substr(sha1($key, true), 0, 16);

        $error = false;
        if ($fpOut = fopen($dest, 'w')) {
            if ($fpIn = fopen($source, 'rb')) {

                $iv = fread($fpIn, 16);
                while (!feof($fpIn)) {

                    $ciphertext = fread(
                        $fpIn,
                        16 * ($this->encrypt_blocks + 1)
                    );

                    $plaintext = openssl_decrypt(
                        $ciphertext,
                        'aes-256-cbc',
                        $key,
                        OPENSSL_RAW_DATA,
                        $iv
                    );

                    $iv = substr($ciphertext, 0, 16);
                    fwrite($fpOut, $plaintext);
                }
                fclose($fpIn);

            } else {
                $error = true;
            }

            fclose($fpOut);

        } else {
            $error = true;
        }

        return $error ? false : $dest;
    }
}