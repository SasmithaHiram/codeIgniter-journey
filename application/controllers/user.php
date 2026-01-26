<?php
defined('BASEPATH') or exit('No direct script access allowed');

class User extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('User_model', 'user');
        $this->load->helper('url');
    }

    public function index()
    {
        $data['users'] = $this->user->get();
        $this->load->view('user_list', $data);
    }

    public function create()
    {
        // Handle JSON (AJAX) payload
        $contentType = $this->input->get_request_header('Content-Type');
        if ($contentType && stripos($contentType, 'application/json') !== false) {
            $raw = $this->input->raw_input_stream;
            $data = json_decode($raw, true) ?: [];
            if (!empty($data['name']) && !empty($data['email'])) {
                $ok = $this->user->create([
                    'name'  => $data['name'],
                    'email' => $data['email']
                ]);
                return $this->output
                    ->set_content_type('application/json')
                    ->set_output(json_encode(['success' => (bool)$ok]));
            }
            return $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(['success' => false, 'error' => 'Invalid payload']));
        }

        // Handle normal form post
        if ($this->input->post()) {
            $this->user->create([
                'name'  => $this->input->post('name'),
                'email' => $this->input->post('email')
            ]);
            return redirect('user');
        }

        $this->load->view('create_user');
    }

    public function edit($id = null)
    {
        if (!$id) show_404();

        $contentType = $this->input->get_request_header('Content-Type');
        if ($contentType && stripos($contentType, 'application/json') !== false) {
            $raw = $this->input->raw_input_stream;
            $data = json_decode($raw, true) ?: [];
            if (!empty($data['name']) && !empty($data['email'])) {
                $ok = $this->user->update($id, [
                    'name'  => $data['name'],
                    'email' => $data['email']
                ]);
                return $this->output
                    ->set_content_type('application/json')
                    ->set_output(json_encode(['success' => (bool)$ok]));
            }
            return $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(['success' => false, 'error' => 'Invalid payload']));
        }

        if ($this->input->post()) {
            $this->user->update($id, [
                'name'  => $this->input->post('name'),
                'email' => $this->input->post('email')
            ]);
            return redirect('user');
        }

        $data['user'] = $this->user->get($id);
        $this->load->view('edit_user', $data);
    }


    public function delete($id)
    {
        $contentType = $this->input->get_request_header('Content-Type');
        if ($contentType && stripos($contentType, 'application/json') !== false) {
            $ok = $this->user->delete($id);
            return $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(['success' => (bool)$ok]));
        }

        $this->user->delete($id);
        return redirect('user');
    }

    public function search()
    {
        $keyword = $this->input->get('q');
        if ($keyword) {
            $data['users'] = $this->user->search(['name', 'email'], $keyword);
        } else {
            $data['users'] = $this->user->get_all();
        }
        $this->load->view('user_list', $data);
    }
}
