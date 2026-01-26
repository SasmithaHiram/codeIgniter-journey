<?php

class User_model extends CI_Model
{
    public function get_users()
    {
        return $this->db->get('users')->result();
    }

    public function get_user($id)
    {
        return $this->db->get_where('users', ['id' => $id])->row();
    }

    public function create_user($data)
    {
        return $this->db->insert('users', $data);
    }

    public function update_user($id, $data)
    {
        return $this->db->where('id', $id)->update('users', $data);
    }

    public function delete($id)
    {
        return $this->db->where('id', $id)->delete('users');
    }

    public function search_user_byId($id)
    {
        $this->db->like('id', $id);
        $query = $this->db->get('users');
        return $query->result();
    }
}
