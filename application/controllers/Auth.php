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

        $user1 = $this->user->get_user_by_email($email);

        if ($user1 && password_verify($password, $user1->password)) {
            $token = $this->jwt_helper->generate_token([
                'id' => $user1->id,
                'email' => $user1->email
            ]);

            return $this->response([
                'status' => true,
                'token' => $token
            ], RestController::HTTP_OK);
        } else {

            return $this->response([
                'status' => false,
                'error' => 'Invalid email or password'
            ], RestController::HTTP_NOT_FOUND);
        }
    }
}
