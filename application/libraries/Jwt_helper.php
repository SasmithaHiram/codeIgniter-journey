<?php
defined('BASEPATH') or exit('No direct script access allowed');

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class Jwt_helper
{
    private $key = "fvbvcbbbbbbbbbbbbbbbbbbbbbbbbbb9dfd383704377y0";
    private $alg = "HS256";

    public function generate_token($payload)
    {
        $issuedAt = time();
        $expire = $issuedAt + (60 * 60);

        $payload = [
            'iat' => $issuedAt,
            'exp' => $expire,
            'data' => $payload
        ];

        return JWT::encode($payload, $this->key, $this->alg);
    }

    public function validate_token($token)
    {
        try {
            $decoded = JWT::decode($token, new Key($this->key, $this->alg));
            return (array) $decoded;
        } catch (Exception $e) {
            return false;
        }
    }
}
