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
		$this->checkSession();
		$data['title'] = "Sign In";
		$this->load->view('prelogin/common/login_header', $data);
		$this->load->view('prelogin/signin');;
	}
	public function signup()
	{
		$this->checkSession();
		$data['title'] = "Sign Up";
		$this->load->view('prelogin/common/login_header', $data);
		$this->load->view('prelogin/signup');
	}
	public function login()
	{
		$this->checkSession();
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


	public function logout()
	{
		$response = array('status' => 'true', 'msg' => messages()['logoutSuccess']);
		$this->session->set_flashdata('toaster', $response);
		$this->session->unset_userdata('userDetails');
		return redirect(base_url('account/signin'));
	}
	public function forgotPassword()
	{
		$this->checkSession();
		$data['title'] = "Forgot Password";
		$this->load->view('prelogin/common/login_header', $data);
		$this->load->view('prelogin/forgot_password');
	}
	//View for sent reset confirmation link
	public function resetLinkSent()
	{
		$this->checkSession();
		$data['title'] = "Reset Link Sent";
		$this->load->view('prelogin/common/login_header', $data);
		$this->load->view('prelogin/reset_link_sent');
	}
	//change password function
	public function changePassword()
	{
		$currentTab = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
		$this->form_validation->set_rules('password', 'Password', 'required');
		$this->form_validation->set_rules('confirmPassword', 'Confirm Password', 'required|matches[password]');
		$this->form_validation->set_rules('token', 'Token', 'required');
		$this->form_validation->set_rules('email', 'Email', 'required');
		if ($this->form_validation->run()) {
			$password = $this->input->post('password');
			$hash = hash('sha256', $password);
			$email= $this->input->post('email');
			$token = $this->input->post('token');
			$checkToken = $this->AccountModel->checkToken($email, $token);
			if ($checkToken) {
				$updatePassword = $this->AccountModel->updatePassword($email, $hash);
				if ($updatePassword) {
					$response = array('status' => 'true', 'msg' => messages()['passwordChanged']);
					$this->session->set_flashdata('toaster', $response);
					return redirect(base_url('account/signin'));
				} else {
					$response = array('status' => 'false', 'msg' => messages()['passwordNotChanged']);
					$this->session->set_flashdata('toaster', $response);
					return redirect(base_url('account/signin'));
				}
			} else {
				$response = array('status' => 'false', 'msg' => messages()['invalidToken']);
				$this->session->set_flashdata('toaster', $response);
				return redirect(base_url('account/forgotPassword'));
			}
		} else {
			$response = array('status' => 'false', 'msg' => messages()['passwordNotMatch']);
			$this->session->set_flashdata('toaster', $response);
			return redirect($currentTab);
		}
	}
	public function resetPassword()
	{
		$this->checkSession();
		$token = $this->input->get('token');
		$email = $this->input->get('email');
		if ((!empty($token) && !empty($email)) && ($token != NULL && $email != NULL)) {
			$checkToken = $this->AccountModel->checkToken($email, $token);
			if ($checkToken) {
				$data['title'] = "Reset Password";
				$this->load->view('prelogin/common/login_header', $data);
				$this->load->view('prelogin/reset_password');
			} else {
				$response = array('status' => 'false', 'msg' => messages()['invalidLink']);
				$this->session->set_flashdata('toaster', $response);
				return redirect(base_url('account/forgotPassword'));
			}
		} else {
			$response = array('status' => 'false', 'msg' => messages()['invalidLink']);
			$this->session->set_flashdata('toaster', $response);
			return redirect(base_url('account/forgotPassword'));
		}
	}

	public function sendresetlink()
	{
		$currentTab = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
		$this->form_validation->set_rules('email', 'Email Id', 'required');
		if ($this->form_validation->run()) {
			$email = strtolower($this->input->post('email'));
			$emailCheck = $this->AccountModel->checkEmail($email);
			if ($emailCheck) {
				$resetLink = $this->createResetLink($email);
				$subject = "Reset Password | Password Manager";
				$message = "Please click on the link below to reset your password. <br> <br><a href='" . $resetLink . "'>Reset Password</a>  <br><br> If you are unable to click on the link, please copy the link given below. <br><br> " . $resetLink . "<br><br> This link will be valid only for 10 minutes.
				<br> If you have not requested for password reset, please ignore this email. <br> <br> Thanks, <br> Password Manager Team";
				$sendMail = $this->sendEmail($email, $subject, $message);
				var_dump($sendMail);
				if ($sendMail) {
					return redirect(base_url('account/resetLinkSent'));
				} else {
					$response = array('status' => 'false', 'msg' => messages()['resetLinkNotSent']);
					$this->session->set_flashdata('toaster', $response);
					return redirect($currentTab);
				}
			} else {
				$response = array('status' => 'false', 'msg' => messages()['userNotExists']);
				$this->session->set_flashdata('toaster', $response);
				return redirect($currentTab);
			}
		} else {
			$response = array('status' => 'false', 'msg' => messages()['enterValidEmail']);
			$this->session->set_flashdata('toaster', $response);
			return redirect($currentTab);
		}
	}
	private function sendEmail($email, $subject, $message)
	{
		$this->load->library('email'); // Note: no $config param needed
		$this->email->from('pandhurnekarviplavdev@gmail.com', 'Password Manager'); // change it to yours
		$this->email->to($email); // change it to yours
		$this->email->subject($subject);
		$this->email->message($message);

		if ($this->email->send()) {

			return true;
		} else {
			return false;
		}
	}
	private function createResetLink($email)
	{
		$this->load->helper('string');
		$token = hash('sha256', $email . random_string('alnum', 16));
		$result = $this->AccountModel->createResetLink($email, $token);
		$resetLink = base_url('account/resetpassword/?token=' . $token . '&email=' . $email);
		return $resetLink;
	}
	private function checkSession()
	{
		if ($this->session->userdata('userDetails')) {
			return redirect(base_url('dashboard'));
		}
	}
}
