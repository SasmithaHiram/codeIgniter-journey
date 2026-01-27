<?php
defined('BASEPATH') or exit('No direct script access allowed');

use \Firebase\JWT\JWT;

class Jwt_helper
{
    private $key = "fvbvcbbbbbbbbbbbbbbbbbbbbbbbbbb9dfd383704377y0";

    public function generate_token($payload)
    {
        $issuedAt = time();
        $expire = $issuedAt + (60 * 60);
        $payload['iat'] = $issuedAt;
        $payload['exp'] = $expire;

        return JWT::encode($payload, $this->key);
    }

    public function validate_token($token)
    {
        try {
            $decoded = JWT::decode($token, $this->key, array('HS256'));
            return (array)$decoded;
        } catch (Exception $e) {
            return false;
        }
    }
}
