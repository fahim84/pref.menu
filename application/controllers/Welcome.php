<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Welcome extends CI_Controller 
{
	var $data;
	
	public function __construct()
	{
		parent::__construct();
		
		# if user is not logged in, then redirect him to login page
		if(! isset($_SESSION[USER_LOGIN]['id']) )
		{
			//redirect('login');
		}
	}
	
	public function index()
	{
		$this->data['Active'] = 'home';
		$this->load->view('index',$this->data);
	}
	
	public function thankyou()
	{
		$this->data['Active'] = 'menu';
		$this->load->view('thankyou',$this->data);
	}
	
	public function about_us()
	{
		$this->data['Active'] = 'about-us';
		$this->load->view('about-us',$this->data);
	}
	
	public function email_feedback($rating_id)
	{
		$this->data['rating_id'] = $rating_id;
		$this->data['Active'] = 'menu';
		$this->load->view('email-feedback',$this->data);
	}
	
	public function config()
	{
		
		my_var_dump('$_SERVER[HTTP_HOST] = '.$_SERVER['HTTP_HOST']);
		my_var_dump('ENVIRONMENT = '.ENVIRONMENT);
		my_var_dump('$this->db->hostname = '.$this->db->hostname);
		my_var_dump('$this->db->username = '.$this->db->username);
		my_var_dump('$this->db->password = '.$this->db->password);
		my_var_dump('$this->db->database = '.$this->db->database);
		
		
	}

}
