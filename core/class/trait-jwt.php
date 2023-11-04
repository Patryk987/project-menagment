<?php

namespace ModuleManager;

class JWT
{
    private string $secret;

    public function __construct(string $secret)
    {
        $this->secret = $secret;
    }

    public function get_token($payload)
    {

        $header = json_encode([
            'typ' => 'JWT',
            'alg' => 'HS256'
        ]);

        $payload = json_encode($payload);

        $base64_url_header = $this->base64_url_encode($header);
        $base64U_url_payload = $this->base64_url_encode($payload);

        $signature = hash_hmac('sha256', $base64_url_header . "." . $base64U_url_payload, $this->secret, true);

        $base64_url_signature = $this->base64_url_encode($signature);

        $token = $base64_url_header . "." . $base64U_url_payload . "." . $base64_url_signature;

        return $token;

    }

    /**
     * Check token
     * @param string $token
     * @return array ["status" => $signatureValid, "header" => $encode_header, "payload" => $encode_payload]
     */
    public function check_token($token)
    {

        if (!empty($token)) {


            $token_parts = explode('.', $token);

            if (
                empty($token_parts[0])
                || empty($token_parts[1])
                || empty($token_parts[2])
            ) {
                return ["status" => false, "header" => [], "payload" => []];
            }

            $base64_header = $token_parts[0];
            $base64_payload = $token_parts[1];
            $signatureProvided = $token_parts[2];

            $signature = hash_hmac('sha256', $base64_header . "." . $base64_payload, $this->secret, true);

            $base64_url_signature = $this->base64_url_encode($signature);


            $signature_valid = ($base64_url_signature === $signatureProvided);

            if ($signature_valid) {
                $encode_header = json_decode(base64_decode($base64_header));
                $encode_payload = json_decode(base64_decode($base64_payload));
                $time_valid = ($encode_payload->create_time <= time() && $encode_payload->expire_time >= time());
                $valid = ($time_valid && $signature_valid);
            } else {
                $encode_header = [];
                $encode_payload = [];
                $valid = $signature_valid;
            }

            return ["status" => $valid, "header" => $encode_header, "payload" => $encode_payload];

        } else {
            return ["status" => false, "header" => [], "payload" => []];
        }




    }

    /**
     * Check token for api
     * @param array $input [token]
     * @return array [$status bool]
     */
    private function check_user_token($input): array
    {

        // $status = false;
        $token = $input["token"];
        if (!empty($token)) {
            $valid = $this->check_token($token);
            return ["status" => $valid['status'], "data" => $valid['payload']];

        } else {
            return ["status" => false, "data" => ""];
        }

    }

    private function base64_url_encode($text)
    {
        return str_replace(
            ['+', '/', '='],
            ['-', '_', ''],
            base64_encode($text)
        );
    }

}