<?php

class BaseModel extends CI_Model
{
    protected $table = "";
    protected $primaryKey = "id";
    protected $timestamps = true;

    public function __construct()
    {
        parent::__construct();
    }

    public function get($id = null)
    {
        if ($id === null) {
            return $this->db->get($this->table)->result();
        } else {
            return $this->db->get_where($this->table, [$this->primaryKey => $id])->row();
        }
    }

    public function get_all()
    {
        return $this->db->get($this->table)->result();
    }

    public function create($data)
    {
        if ($this->timestamps) {
            $data['created_at'] = date('Y-m-d H:i:s');
            $data['updated_at'] = date('Y-m-d H:i:s');
        }
        return $this->db->insert($this->table, $data);
    }

    public function update($id, $data)
    {
        if ($this->timestamps) {
            $data['updated_at'] = date('Y-m-d H:i:s');
        }
        return $this->db->where($this->primaryKey, $id)->update($this->table, $data);
    }

    public function delete($id)
    {
        return $this->db->where($this->primaryKey, $id)->delete($this->table);
    }
}
