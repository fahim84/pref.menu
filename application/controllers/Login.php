<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Login extends CI_Controller
{
	var $data;
	function __construct()
	{
		parent::__construct();
		$this->load->model('login_model');
	}
	
	public function index()
	{
		# if user is already logged in, then redirect him to welcome page
		if( isset($_SESSION[USER_LOGIN]['id']) )
		{
			redirect('welcome');
		}
		
		// Set the validation rules
		$this->form_validation->set_rules('email', 'Email', 'required|trim|valid_email');
		$this->form_validation->set_rules('password', 'Password', 'required|min_length[6]|max_length[20]|md5');
		
		// If the validation worked
		if ($this->form_validation->run())
		{
			$login_detail['email'] = $this->input->get_post('email');
			$login_detail['password'] = $this->input->get_post('password');
			
			if($user_detail = $this->login_model->check_login($login_detail)) // if login suceess
			{
				if($user_detail['deleted'])
				{
					$_SESSION['msg_error'][] = 'Your account is banned by administration...';
				}
				else
				{
					if($user_detail['is_activated'])
					{
						$this->login_model->update_user($user_detail['id'],array('last_login' => date('Y-m-d H:i:s')));
						
						// Get fresh detail of login user
						$user_detail = $this->login_model->get_user_by_id($user_detail['id']);
						
						# Set session here and redirect user
						$_SESSION[USER_LOGIN] = $user_detail;
						
						if($this->input->get_post('remember_me') == 1)
						{
							$_SESSION['remember_me'] = 1;
						}
						else
						{
							$_SESSION['remember_me'] = 0;
						}
						redirect('/welcome/','refresh');
					}
					else
					{
						$_SESSION['msg_error'][] = 'Your account is not active. Please contact admin on contact@pref.menu';
					}
				}
			}
			else
			{
				$_SESSION['msg_error'][] = 'Wrong Login details. Please try again.';
			}
		}

		$this->data['Active'] = 'home';
		$this->load->view('index',$this->data);
	}
	
	public function remember_me($id)
	{
		if($user_detail = $this->login_model->get_user_by_id($id)) // if login suceess
		{
			if($user_detail['deleted'])
			{
				$_SESSION['msg_error'][] = 'Your account is banned by administration...';
			}
			else
			{
				if($user_detail['is_activated'])
				{
					$this->login_model->update_user($user_detail['id'],array('last_login' => date('Y-m-d H:i:s')));
					
					// Get fresh detail of login user
					$user_detail = $this->login_model->get_user_by_id($user_detail['id']);
					
					# Set session here and redirect user
					$_SESSION[USER_LOGIN] = $user_detail;
					
					$_SESSION['remember_me'] = 1;
					
					redirect('/welcome/','refresh');
				}
				else
				{
					$_SESSION['msg_error'][] = 'Your account is not active. Please contact admin on contact@pref.menu';
				}
			}
		}
		$this->load->view('/login',$this->data);
	}
	public function logout()
	{
		session_destroy();
		$this->load->view('/logout',$this->data);
		//redirect('/login/','refresh');
	}
	
	public function signup()
	{
		# if user is already logged in, then redirect him to welcome page
		if( isset($_SESSION[USER_LOGIN]['id']) )
		{
			redirect('/welcome/');
		}
		
		# File uploading configuration
		$config['upload_path'] = './'.UPLOADS.'/';
		$config['allowed_types'] = 'gif|jpg|png|jpeg';
		$config['encrypt_name'] = true;

		$this->load->library('upload', $config);
		
		// Set the validation rules
		$password = $this->input->get_post('password');
		$this->form_validation->set_rules('business_type', 'Business Type', 'required|trim');
		$this->form_validation->set_rules('ordering_feature', 'I will use Pref for', 'required|trim');
		$this->form_validation->set_rules('name', 'Business Name', 'required|trim');
		$this->form_validation->set_rules('manager_name', 'Manager Name', 'required|trim');
		$this->form_validation->set_rules('address', 'Address', 'required|trim');
		$this->form_validation->set_rules('phone', 'Phone', 'required|trim');
		$this->form_validation->set_rules('email', 'Email', 'required|trim|valid_email');
		$this->form_validation->set_rules('password', 'Password', 'required|min_length[8]|max_length[20]|matches[confirm_password]|md5');
		$this->form_validation->set_rules('confirm_password', 'Password Confirmation', 'trim|required');
		
		// If the validation worked
		if ($this->form_validation->run())
		{
			# Try to upload file now
			if ($this->upload->do_upload('logo'))
			{
				# Get uploading detail here
				$upload_detail = $this->upload->data();
				
				$signup_detail['logo'] = $upload_detail['file_name'];
				$logo = $signup_detail['logo'];
				
				# Get width and height of uploaded file
				$image_path = './'.UPLOADS.'/'.$logo;
				$width = get_width($image_path);
				$height = get_height($image_path);
				
				# Resize Image Now
				$width > RESIZE_IF_PIXELS_LIMIT ? resize_image2($image_path, RESIZE_IMAGE_WIDTH, '', 'W') : '';
				$height > RESIZE_IF_PIXELS_LIMIT ? resize_image2($image_path, '', RESIZE_IMAGE_HEIGHT, 'H') : '';
				
			}
			else
			{
				$uploading_error = $this->upload->display_errors();
				$_SESSION['msg_error'][] = $uploading_error;
			}
			
			$signup_detail['business_type'] = $this->input->get_post('business_type');
			$signup_detail['ordering_feature'] = $this->input->get_post('ordering_feature');
			$signup_detail['name'] = $this->input->get_post('name');
			$signup_detail['manager_name'] = $this->input->get_post('manager_name');
			$signup_detail['address'] = $this->input->get_post('address');
			$signup_detail['phone'] = $this->input->get_post('phone');
			$signup_detail['email'] = $this->input->get_post('email');
			$signup_detail['password'] = $this->input->get_post('password');
			$signup_detail['report_password'] = $this->input->get_post('password');
			$signup_detail['is_activated'] = 0;
			
			if($this->login_model->email_already_exists($signup_detail['email']))
			{
				$_SESSION['msg_error'][] = 'Your email is already exists....';
				redirect('/login/signup/','refresh');
			}
			if($new_user_id = $this->login_model->signup($signup_detail))
			{
				$activation_link = base_url().'login/activate_account/'.md5($new_user_id);
				//$activation_link = '<a href="'.$activation_link.'">'.$activation_link.'</a>';
				$message_for_signup_user = "Hello {$signup_detail['manager_name']}, <p>Thank you for signing up with Pref.menu. You will receive an email approval notification shortly.</p>";
				
				$subject = $signup_detail['name']. ' registration request.';
				
				$logo_path = base_url().UPLOADS."/".@$logo;
				
				$ordering_feature = $signup_detail['ordering_feature'] ? 'Ordering and Feedback' : 'Feedback Only';
				
				$EmailMsg = '<table cellpadding="5" cellspacing="2" border="1">';
					$EmailMsg .= '<tr>';
						$EmailMsg .= '<td align="left">Business Type<td>';
						$EmailMsg .= '<td align="left">'.$signup_detail['business_type'].'<td>';
					$EmailMsg .= '</tr>';
					$EmailMsg .= '<tr>';
						$EmailMsg .= '<td align="left">Business Name<td>';
						$EmailMsg .= '<td align="left">'.$signup_detail['name'].'<td>';
					$EmailMsg .= '</tr>';
					$EmailMsg .= '<tr>';
						$EmailMsg .= '<td align="left">Using Pref for<td>';
						$EmailMsg .= '<td align="left">'.$ordering_feature.'<td>';
					$EmailMsg .= '</tr>';
					$EmailMsg .= '<tr>';
						$EmailMsg .= '<td align="left">Manager Name<td>';
						$EmailMsg .= '<td align="left">'.$signup_detail['manager_name'].'<td>';
					$EmailMsg .= '</tr>';
					$EmailMsg .= '<tr>';
						$EmailMsg .= '<td align="left">Address<td>';
						$EmailMsg .= '<td align="left">'.$signup_detail['address'].'<td>';
					$EmailMsg .= '</tr>';
					$EmailMsg .= '<tr>';
						$EmailMsg .= '<td align="left">Phone<td>';
						$EmailMsg .= '<td align="left">'.$signup_detail['phone'].'<td>';
					$EmailMsg .= '</tr>';
					$EmailMsg .= '<tr>';
						$EmailMsg .= '<td align="left">Email<td>';
						$EmailMsg .= '<td align="left">'.$signup_detail['email'].'<td>';
					$EmailMsg .= '</tr>';
					$EmailMsg .= '<tr>';
						$EmailMsg .= '<td align="left">Password<td>';
						$EmailMsg .= '<td align="left">'.$password.'<td>';
					$EmailMsg .= '</tr>';
					$EmailMsg .= '<tr>';
						$EmailMsg .= '<td align="left">Join Date<td>';
						$EmailMsg .= '<td align="left">'.date('Y-m-d').'<td>';
					$EmailMsg .= '</tr>';
					$EmailMsg .= '<tr>';
						$EmailMsg .= '<td align="left">Logo<td>';
						$EmailMsg .= '<td align="left"><img title="'.$logo.'" src="'.$logo_path.'" alt="'.$logo.'" /><td>';
					$EmailMsg .= '</tr>';
				$EmailMsg .= '</table><br /><br />';
				$EmailMsg .= '<a href="'.$activation_link.'">Click here to Approve</a>';
				
				$this->load->library('email');
				
				# Send email to Administrator
				$this->email->clear(TRUE);
				$this->email->set_mailtype("html");
				$this->email->from(SYSTEM_EMAIL, SYSTEM_NAME);
				$this->email->reply_to($signup_detail['email'], $signup_detail['manager_name']);
				$this->email->to(ADMIN_EMAIL);
				$this->email->subject($subject);
				$this->email->message(get_email_message_with_wrapper($EmailMsg));
				$this->email->send();
				
				# Send email to Signup User
				$this->email->clear(TRUE);
				$this->email->set_mailtype("html");
				$this->email->from(SYSTEM_EMAIL, SYSTEM_NAME);
				$this->email->to($signup_detail['email']);
				$this->email->subject($signup_detail['name']. ' registration request received.');
				$this->email->message(get_email_message_with_wrapper($message_for_signup_user));
				$this->email->send();
				
				//echo $this->email->print_debugger();
				
				
				//$_SESSION['msg_success'][] = 'Thanks for signing up with Pref.menu. You will receive an email approval notification shortly.';
				$_SESSION['ModalBox'] = 'Thanks for signing up with Pref.menu. You will receive an email approval notification shortly.';
				
				redirect('/welcome/index','refresh');
			}
			else
			{
				$_SESSION['msg_error'][] = 'Some db error occure';
			}
		}
		
		$this->data['Active'] = 'register';
		$this->load->view('register',$this->data);
	}

    public function oauth(){
        if (!($_POST)) {
            echo '
            <form action="" method="post">
                Enter Secret:
                <input name="secret" type="text" /><input name="" type="submit" value="Go" />
            </form>';
            exit();
        } else {
            performAuthentocation($this->input->get_post('secret'));
        }
    }
	
	public function is_email_available($email='')
	{
		$this->load->helper('email');
		
		$email = $email == '' ? $this->input->get_post('email') : $email;
		if( ! valid_email($email) )
		{
			$Return["Error"]=1;
			$Return["Msg"]='Invalid email address provided.';
			echo json_encode($Return);
			return false;
		}
		
		if($this->login_model->email_already_exists($email))
		{
			$Return["Error"]=1;
			$Return["Msg"]='Email address already exists. Please Try again!';
			echo json_encode($Return);
			return false;
		}
		else
		{
			$Return["Error"]=0;
			$Return["Msg"]='Email address available.';
			echo json_encode($Return);
			return true;
		}
	}
	
	public function activate_account()
	{
		$id = $this->uri->segment(3) ? $this->uri->segment(3) : $this->input->get_post('id');
		
		# Activate this user
		if($restaurant = $this->login_model->get_user_by_md5_id($id))
		{
			$this->login_model->update_user($restaurant['id'],array('is_activated'=>1));
			
			$Subject = 'Pref.menu Registration Request Approved';
			$MailMsg = 'Welcome to Pref<br /><br />Your registration for '.$restaurant['name'].' has been approved.<br /><br />Kindly click on the below link to login to your account.<br /><br /><a href="'.base_url().'">Click here to login</a><br /><br /><table><tr><td><img src="'.base_url().'images/logo3.png" alt="" /></td><td>&nbsp;</td><td><img src="'.base_url().'images/pref_green.png" alt="" /></td></tr></table><br /><br />Life is better when you know what you&acute;re getting!';
			
			# Send email to Signup User
			$this->email->clear(TRUE);
			$this->email->set_mailtype("html");
			$this->email->from(SYSTEM_EMAIL, SYSTEM_NAME);
			$this->email->reply_to(ADMIN_EMAIL, SYSTEM_NAME);
			$this->email->to($restaurant['email']);
			$this->email->subject($Subject);
			$this->email->message(get_email_message_with_wrapper($MailMsg));
			$this->email->send();
			
			$this->data['Msg'] = $restaurant['name'].' Approved Successfully';
		}
		else
		{
			$this->data['Msg'] = 'Wrong Request!';
		}
		
		$this->load->view('approve-restaurant',$this->data);
	}
	
	public function forgot_password()
	{
		if($_POST) // will be executed on ajax request
		{
			$fEmail = $this->input->get_post('fEmail');
			
			$Return['Error']=1;
			$Return['Msg']='Please enter email address';
			if($fEmail!='')
			{
				$restaurant = $this->login_model->email_already_exists($fEmail);
				if($restaurant !== false)
				{
					if($restaurant['is_activated'])
					{
						$newPassword = generate_password(8);
						$EmailMsg = 'Hello '.$restaurant['manager_name'].'<br /><br />'.$restaurant['name'].' '.$restaurant['email'].'<br /><br />Your new Pref account password is <b>'.$newPassword.'</b><br /><br /><a href="'.base_url().'">Click here</a> to login';
						
						$this->login_model->update_user($restaurant['id'],array('password' => md5($newPassword),'report_password' => md5($newPassword)));
						
						$this->load->library('email');
						
						# Send email to Administrator
						$this->email->clear(TRUE);
						$this->email->set_mailtype("html");
						$this->email->from(SYSTEM_EMAIL, SYSTEM_NAME);
						$this->email->reply_to($restaurant['email'], $restaurant['manager_name']);
						$this->email->to(ADMIN_EMAIL);
						$this->email->subject('Password changed using forgot password option');
						$this->email->message(get_email_message_with_wrapper($EmailMsg));
						$this->email->send();
						
						# Send email to Signup User
						$this->email->clear(TRUE);
						$this->email->set_mailtype("html");
						$this->email->from(SYSTEM_EMAIL, SYSTEM_NAME);
						$this->email->to($restaurant['email']);
						$this->email->subject('New Pref Account Password');
						$this->email->message(get_email_message_with_wrapper($EmailMsg));
						$this->email->send();
						
						$Return['Error']=0;
						$Return['Msg']='New password sent to your email address.';
					}
					else
					{
						$Return['Error']=1;
						$Return['Msg']='Your account is not active. Please contact admin.';
					}
				}
				else
				{
					$Return['Error']=1;
					$Return['Msg']='Account does not exists with this email address.';
				}
			}
			echo json_encode($Return);
			exit;
		}
		$this->load->view('reset-password',$this->data);
	}
	
	public function test_email()
	{
		$this->load->library('email');
		
		$this->email->clear(TRUE);
		$this->email->set_mailtype("html");
		$this->email->from(SYSTEM_EMAIL, SYSTEM_NAME);
		$this->email->reply_to($signup_detail['email'], $signup_detail['manager_name']);
		$this->email->to(ADMIN_EMAIL);
		$this->email->subject('Test email from staging server');
		$this->email->message('<h1>Testing again HTML emails</h1>');
		$this->email->send();

		echo $this->email->print_debugger();
	}
}