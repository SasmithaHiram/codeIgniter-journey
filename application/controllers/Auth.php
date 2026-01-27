<?php

use chriskacerguis\RestServer\RestController;

defined('BASEPATH') or exit('No direct script access allowed');

class Auth extends RestController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('User_model', 'user');
        $this->load->library('Jwt_helper');
        $this->load->helper(['url', 'form']);
    }

    public function index_post()
    {
        $email = trim($this->post('email'));
        $password = trim($this->post('password'));

        if ($email === '' || $password === '') {
            return $this->response([
                'status' => false,
                'error' => 'Email and password are required'
            ], RestController::HTTP_BAD_REQUEST);
        }

        $user = $this->user->get_user_by_email($email);
        if ($user && password_verify($password, $user->password)) {
            $token = $this->jwt_helper->generate_token([
                'id' => $user->id,
                'email' => $user->email
            ]);
            $this->response([
                'status' => true,
                'token' => $token
            ], RestController::HTTP_OK);
        } else {
            $this->response([
                'status' => false,
                'error' => 'Invalid email or password'
            ], RestController::HTTP_UNAUTHORIZED);
        }
    }
}
