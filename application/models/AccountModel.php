<?php
defined('BASEPATH') or exit('No direct script access allowed');

class AccountModel extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function checkEmail($email)
    {
        $this->db->select('userId');
        $this->db->from('users');
        $this->db->where('userEmail', $email);
        $query = $this->db->get();
        $result = $query->row_array();
        return $result;
    }

    public function login($email, $hash)
    {
        $this->db->select('userId,userName,userEmail,userStatus,userMobile,userEmailVerify,userMobileVerify');
        $this->db->from('users');
        $this->db->where('userEmail', $email);
        $this->db->where('userHash', $hash);
        $query = $this->db->get();
        $result = $query->row_array();
        return $result;
    }
    
    
}