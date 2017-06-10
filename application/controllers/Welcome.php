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
	
	public function afterthankyou()
	{
		$this->data['Active'] = 'menu';
		$this->load->view('thankyouafter',$this->data);
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
	
	public function testemail() 
	{
		error_reporting(E_ALL);
		ini_set('display_errors', 1);
		
		$ci = get_instance(); // CI_Loader instance
		$ci->load->config('email');
		my_var_dump( $ci->config->item('allconfig'));
		
		$this->load->library('email');
		$this->email->set_newline("\r\n");
		
		// Set to, from, message, etc.
		$to[] = "volcano_ck@hotmail.com";
		$to[] = "fahim@blazebuddies.com";
		
		$this->email->to($to);
		
		$this->email->from(SYSTEM_EMAIL);
		$this->email->subject('Testing email from '.base_url());
		$this->email->message("Testing email body <br>".json_encode($ci->config->item('allconfig'),JSON_PRETTY_PRINT));
		
		$result = $this->email->send();
		my_var_dump( $result);
		//my_var_dump( $this->email->print_debugger());
	}
	
	public function testemail2() 
	{
		error_reporting(E_ALL);
		ini_set('display_errors', 1);
		
		$this->load->library('email');
		$this->email->set_newline("\r\n");
		
		// Set to, from, message, etc.
		$to[] = "volcano_ck@hotmail.com";
		$to[] = "fahim@blazebuddies.com";
		
		$this->email->to($to);
		
		$this->email->from(SYSTEM_EMAIL);
		$this->email->subject('Testing email from '.base_url());
		$this->email->message("Testing email body <br>");
		
		$result = $this->email->send();
		my_var_dump( $result);
		//my_var_dump( $this->email->print_debugger());
	}

}
