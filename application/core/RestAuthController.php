<?php

use chriskacerguis\RestServer\RestController;

defined('BASEPATH') or exit('No direct script access allowed');

class RestAuthController extends RestController
{
    protected $auth_payload = null;
    protected $auth_user = null;
    public $jwt_helper;

    public function __construct()
    {
        parent::__construct();
        $this->load->library('Jwt_helper');
        $authHeader = $this->resolveAuthorizationHeader();
        $token = $this->extractBearerToken($authHeader);

        if (!$token) {
            $this->unauthorized('Missing Authorization Bearer token');
            exit;
        }

        $decoded = $this->jwt_helper->validate_token($token);
        if (!$decoded) {
            $this->unauthorized('Invalid or expired token');
            exit;
        }

        $this->auth_payload = $decoded;

        if (is_array($decoded) && isset($decoded['data'])) {
            $this->auth_user = $decoded['data'];
        } elseif (is_object($decoded) && isset($decoded->data)) {
            $this->auth_user = $decoded->data;
        }
    }

    protected function resolveAuthorizationHeader()
    {

        if (!empty($_SERVER['HTTP_AUTHORIZATION'])) {
            return $_SERVER['HTTP_AUTHORIZATION'];
        }
        if (!empty($_SERVER['Authorization'])) {
            return $_SERVER['Authorization'];
        }
        if (function_exists('apache_request_headers')) {
            $headers = apache_request_headers();
            if (isset($headers['Authorization'])) {
                return $headers['Authorization'];
            }

            foreach ($headers as $key => $value) {
                if (strtolower($key) === 'authorization') {
                    return $value;
                }
            }
        }
        return null;
    }

    protected function extractBearerToken($authHeader)
    {
        if (!$authHeader) {
            return null;
        }

        if (stripos($authHeader, 'Bearer ') === 0) {
            return trim(substr($authHeader, 7));
        }
        return null;
    }

    protected function unauthorized($message)
    {
        return $this->response([
            'status' => false,
            'error'  => $message
        ], RestController::HTTP_UNAUTHORIZED);
    }

    public function getAuthUser()
    {
        return $this->auth_user;
    }
}
