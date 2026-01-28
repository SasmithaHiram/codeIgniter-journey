<?php

use chriskacerguis\RestServer\RestController;

defined('BASEPATH') or exit('No direct script access allowed');

require_once APPPATH . 'validations/UserValidator.php';
require_once APPPATH . 'core/RestAuthController.php';

/**
 * @property User_model $user
 * @property CI_Cache $cache
 * @property CI_DB_query_builder $db
 */

class User extends RestAuthController

{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('User_model', 'user');
        $this->load->helper('url');
        $this->load->driver('cache');
    }

    // GET /user? id={id} | q={search}
    public function index_get()
    {
        $id = $this->get('id');
        $keyword = $this->get('q');

        if ($id !== null && $id !== '') {

            $cacheKey = 'user_id_' . $id;

            $user = $this->cache->get($cacheKey);

            if ($user === FALSE) {
                $user = $this->user->get($id);

                if ($user) {
                    $this->cache->save($cacheKey, $user, 300);
                }
            }

            if ($user) {
                return $this->response([
                    'success' => true,
                    'data' => $user
                ], RestController::HTTP_OK);
            }

            return $this->response([
                'success' => false,
                'error' => 'User not found'
            ], RestController::HTTP_NOT_FOUND);
        }

        $cacheKey = 'users_all';

        $users = $this->cache->get($cacheKey);

        if ($users === FALSE) {

            $users = $this->user->get_all();

            $this->cache->save($cacheKey, $users, 120);
        }

        return $this->response([
            'success' => true,
            'data' => $users
        ], RestController::HTTP_OK);
    }


    // POST /user
    public function index_post()
    {
        $data = [
            'name' => trim($this->post('name')),
            'email' => trim($this->post('email')),
            'password' => password_hash(trim($this->post('password')), PASSWORD_DEFAULT)
        ];

        $errors = UserValidator::validateCreate($data);
        if (!empty($errors)) {
            return $this->response([
                'success' => false,
                'errors' => $errors
            ], RestController::HTTP_BAD_REQUEST);
        }

        $ok = $this->user->create($data);

        if ($ok) {
            $id = $this->db->insert_id();
            return $this->response([
                'success' => true,
                'id' => $id
            ], RestController::HTTP_CREATED);
        }
        return $this->response([
            'success' => false,
            'error' => 'Failed to create user'
        ], RestController::HTTP_INTERNAL_ERROR);
    }

    // PUT /user? id={id}
    public function index_put()
    {
        $id = $this->get('id');
        if ($id === null || $id === '') {
            $id = $this->put('id');
        }

        if ($id === null || $id === '') {
            return $this->response([
                'success' => false,
                'error' => 'Missing user id'
            ], RestController::HTTP_BAD_REQUEST);
        }

        $payload = [
            'name'  => $this->put('name'),
            'email' => $this->put('email'),
            'password' => password_hash($this->put('password'), PASSWORD_DEFAULT)
        ];

        if (empty($payload['name']) || empty($payload['email']) || empty($payload['password'])) {
            return $this->response([
                'success' => false,
                'error' => 'Name, Email, and Password are required'
            ], RestController::HTTP_BAD_REQUEST);
        }

        $existing = $this->user->get($id);
        if (!$existing) {
            return $this->response([
                'success' => false,
                'error' => 'User not found'
            ], RestController::HTTP_NOT_FOUND);
        }

        $ok = $this->user->update($id, $payload);
        return $this->response([
            'success' => (bool)$ok
        ], RestController::HTTP_OK);
    }

    public function index_delete($id = null)
    {
        if ($id === null) {
            return $this->response([
                'success' => false,
                'error' => 'Missing user id'
            ], RestController::HTTP_BAD_REQUEST);
        }

        $existing = $this->user->get($id);
        if (!$existing) {
            return $this->response([
                'success' => false,
                'error' => 'User not found'
            ], RestController::HTTP_NOT_FOUND);
        }

        $ok = $this->user->delete($id);
        if ($ok) {
            return $this->response(null, RestController::HTTP_OK);
        }

        return $this->response([
            'success' => false,
            'error' => 'Failed to delete user'
        ], RestController::HTTP_INTERNAL_ERROR);
    }
}
