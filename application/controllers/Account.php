<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Account extends CI_Controller
{


	function __construct()
	{
		parent::__construct();;
		$this->load->helper('message');
		//load encryption library
		$this->load->library('encryption');
		$this->load->model('AccountModel');
		
	}
	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see https://codeigniter.com/userguide3/general/urls.html
	 */
	public function index()
	{
		
		$this->load->view('prelogin/common/login_header');
	}

	public function signin()
	{
		if($this->session->userdata('userDetails')){
			return redirect(base_url('dashboard'));
		}
		$data['title'] = "Sign In";
		$this->load->view('prelogin/common/login_header', $data);
		$this->load->view('prelogin/signin');
		;
	}
	public function signup()
	{
		if($this->session->userdata('userDetails')){
			return redirect(base_url('dashboard'));
		}
		$data['title'] = "Sign Up";
		$this->load->view('prelogin/common/login_header', $data);
		$this->load->view('prelogin/signup');
	}
	public function login()
	{
		if($this->session->userdata('userDetails')){
			return redirect(base_url('dashboard'));
		}
		$currentTab = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
		$this->form_validation->set_rules('email', 'Email Id', 'required');
		$this->form_validation->set_rules('password', 'Password', 'required');


		if ($this->form_validation->run()) {
			$email = $this->input->post('email');
			$password = $this->input->post('password');
			$hash = hash('sha256', $password);
			$emailCheck = $this->AccountModel->checkEmail($email);
			$userDetails = $this->AccountModel->login($email, $hash);

			if ($userDetails) {
				$this->session->set_userdata('userDetails', $userDetails);
				return redirect(base_url('dashboard'));
			} else {
				if ($emailCheck) {
					$response = array('status' => 'false', 'msg' => messages()['invalidUserEmailPass']);
				} else {
					$response = array('status' => 'false', 'msg' => messages()['userNotExists']);
				}
				$this->session->set_flashdata('toaster', $response);
				return redirect($currentTab);
			}
		} else {
			$response = array('status' => 'false', 'msg' => messages()['enterValidEmailPass']);
			$this->session->set_flashdata('toaster', $response);
			return redirect($currentTab);
		}
	}
	
	public function checkSession(){
		if($this->session->userdata('userDetails')){
			return redirect(base_url('dashboard'));
		}
		
	}
	public function logout()
	{
		$response = array('status' => 'true', 'msg' => messages()['logoutSuccess']);
			$this->session->set_flashdata('toaster', $response);
		$this->session->unset_userdata('userDetails');
		return redirect(base_url('account/signin'));
	}
}
