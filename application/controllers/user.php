<?php
defined('BASEPATH') or exit('No direct script access allowed');

class User extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->helper('url');
        $this->load->model('User_model', 'user');
    }

    public function index()
    {
        $data['users'] = $this->user->get_users();
        $this->load->view('user_list', $data);
        $this->load->helper('url');
    }

    public function get_users()
    {
        $users = $this->user->get_users();

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($users));
    }

    public function create()
    {

        $json = json_decode($this->input->raw_input_stream, true);

        if (!empty($json) && isset($json['name'], $json['email'])) {
            $userData = [
                'name' => $json['name'],
                'email' => $json['email']
            ];

            $this->user->create_user($userData);

            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => 'success',
                    'message' => 'User created'
                ]));

            return;
        }

        $name  = $this->input->post('name');
        $email = $this->input->post('email');

        if (!empty($name) && !empty($email)) {
            $userData = [
                'name'  => $name,
                'email' => $email
            ];

            $this->user->create_user($userData);

            redirect('user');
            return;
        }

        show_error('Invalid input data', 400);
    }

    public function delete($id)
    {
        if ($this->user->delete($id)) {
            echo "Deleted successfully";
        } else {
            echo "Delete failed";
        }
        exit;
    }



    public function edit($id = null)
    {
        if (!$id) {
            show_404();
        }


        $input = json_decode(file_get_contents('php://input'), true);

        if ($input && isset($input['name']) && isset($input['email'])) {

            $userData = [
                'name'  => $input['name'],
                'email' => $input['email']
            ];

            $this->user->update_user($id, $userData);

            echo json_encode([
                'status'  => 'success',
                'message' => 'User updated',
                'data'    => $userData
            ]);
        } else {
            echo json_encode([
                'status'  => 'error',
                'message' => 'Invalid input'
            ]);
        }
    }
}
