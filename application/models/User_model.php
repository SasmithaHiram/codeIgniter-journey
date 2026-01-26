<?php
defined('BASEPATH') or exit('No direct script access allowed');

require_once APPPATH . 'core/BaseModel.php';

class User_model extends BaseModel
{
    protected $table = "users";
    protected $primaryKey = "id";
    protected $timestamps = false;
}
