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
    public function createResetLink($email, $token)
    {
        $time = time() + 600;// 10 minutes validity
        $this->db->where('userEmail', $email);
        $this->db->update('users', array('userResetToken' => $token, 'userResetExpire' => $time));
        return $this->db->affected_rows();
    }
    public function checkToken($email, $token)
    {
        $this->db->select('userResetExpire');
        $this->db->from('users');
        $this->db->where('userEmail', $email);
        $this->db->where('userResetToken', $token);
        $query = $this->db->get();
        $result = $query->row_array();
        if ($result['userResetExpire'] > time()) {
            return true;
        }
        return false;
    }
    public function updatePassword($email, $hash)
    {
        $this->db->where('userEmail', $email);
        $this->db->update('users', array('userHash' => $hash, 'userResetToken' => '', 'userResetExpire' => ''));
        return $this->db->affected_rows();
    }
}
