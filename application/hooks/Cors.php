<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Cors
{
    public function enable_cors()
    {
        $allowed_methods = 'GET, POST, PUT, DELETE, OPTIONS';
        $allowed_headers = 'Content-Type, Authorization, X-Requested-With';
        $max_age = '86400';

        // Allow any origin (no credentials)
        header('Access-Control-Allow-Origin: *');
        header('Vary: Origin');
        header('Access-Control-Allow-Methods: ' . $allowed_methods);
        // Echo requested headers if provided by browser (case-insensitive),
        // otherwise fall back to a safe default list
        if (!empty($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS'])) {
            header('Access-Control-Allow-Headers: ' . $_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']);
        } else {
            header('Access-Control-Allow-Headers: ' . $allowed_headers);
        }
        header('Access-Control-Max-Age: ' . $max_age);
        // If you need cookies/credentials, replace '*' with a specific
        // origin and uncomment the line below:
        // header('Access-Control-Allow-Credentials: true');

        // Preflight short-circuit
        if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            header('Content-Type: text/plain; charset=utf-8');
            http_response_code(204);
            exit;
        }
    }
}
