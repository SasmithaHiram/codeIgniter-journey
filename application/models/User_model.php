<?php
defined('BASEPATH') or exit('No direct script access allowed');

require_once APPPATH . 'core/BaseModel.php';

class User_model extends BaseModel
{
    protected $table = "users";
    protected $primaryKey = "id";
    protected $timestamps = false;

    public function search($columns, $keyword)
    {
        if (!is_array($columns)) {
            $columns = [$columns];
        }
        if (empty($columns) || $keyword === null || $keyword === '') {
            return $this->get_all();
        }

        $this->db->like($columns[0], $keyword);
        for ($i = 1; $i < count($columns); $i++) {
            $this->db->or_like($columns[$i], $keyword);
        }
        return $this->db->get($this->table)->result();
    }

    public function get_user_by_email($email)
    {
        $query = $this->db->query("SELECT * FROM {$this->table} WHERE email = ? LIMIT 1", array($email));
        return $query->row();
    }
}
