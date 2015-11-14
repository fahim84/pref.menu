<?php
class Login_model extends CI_Model
{
	function __construct()
	{
		parent::__construct();
	}
	
	public function check_login($login)
	{
		$query = $this->db->get_where('restaurants',array('email'=>$login['email'], 'password' => $login['password']));
		return $query->num_rows() ? $query->row_array() : false;
	}
	
	public function signup($data)
	{
		$data['date_created'] = date('Y-m-d H:i:s');
		$data['date_updated'] = date('Y-m-d H:i:s');
		
		if($this->db->insert('restaurants', $data))
		{
			return $this->db->insert_id();
		}
		return false;
	}
	
	public function update_user($id,$data)
	{
		$data['date_updated'] = date('Y-m-d H:i:s');
		$this->db->where('id', $id);
		return $this->db->update('restaurants',$data);
	}
	
	public function get_user_by_id($id)
	{
		$query = $this->db->get_where('restaurants',array('id'=>$id));
		return $query->num_rows() ? $query->row_array() : false;
	}
	
	public function get_user_by_md5_id($md5_id)
	{
		$this->db->where('MD5(id)', "'$md5_id'", FALSE);
		$query = $this->db->get('restaurants');
		return $query->num_rows() ? $query->row_array() : false;
	}
	
	public function email_already_exists($email)
	{
		$query = $this->db->get_where('restaurants',array('email'=>$email));
		return $query->num_rows() ? $query->row_array() : false;
	}
	
	public function email_admin_profile_changes($old_data,$new_data)
	{
		$old_data['password'] = 'Encrypted Password';
		if($new_data['password'] == '')
		{
			$new_data['password'] = 'Not Changed';
		}
		
		if(file_exists('./'.UPLOADS.'/'.$old_data['logo']))
		{
			if(is_file('./'.UPLOADS.'/'.$old_data['logo']))
			{
				$old_data['logo'] = '<img src="'.base_url().UPLOADS.'/'.$old_data['logo'].'" alt="logo" title="'.$old_data['logo'].'" width="100px" >';
			}
		}
		if(file_exists('./'.UPLOADS.'/'.$new_data['logo']))
		{
			if(is_file('./'.UPLOADS.'/'.$new_data['logo']))
			{
				$new_data['logo'] = '<img src="'.base_url().UPLOADS.'/'.$new_data['logo'].'" alt="logo" title="'.$new_data['logo'].'" width="100px" >';
			}
		}
		
		$old_data['ordering_feature'] = $old_data['ordering_feature'] ? 'Ordering and Feedback' : 'Feedback Only';
		$new_data['ordering_feature'] = $new_data['ordering_feature'] ? 'Ordering and Feedback' : 'Feedback Only';
		
		$html = '	
			<table border="1" cellpadding="2" cellspacing="2">
			  <caption>
				Restaurant Profile Updated
			  </caption>
			  <tbody>
				<tr>
				  <th>Data</th>
				  <th>Old</th>
				  <th>New</th>
				</tr>
				<tr>
				  <td>ID</td>
				  <td>'.$old_data['id'].'</td>
				  <td>'.$old_data['id'].'</td>
				</tr>
				<tr>
				  <td>Name</td>
				  <td>'.$old_data['name'].'</td>
				  <td>'.$new_data['name'].'</td>
				</tr>
				<tr>
				  <td>Using Pref for</td>
				  <td>'.$old_data['ordering_feature'].'</td>
				  <td>'.$new_data['ordering_feature'].'</td>
				</tr>
				<tr>
				  <td>Business Type</td>
				  <td>'.$old_data['business_type'].'</td>
				  <td>'.$new_data['business_type'].'</td>
				</tr>
				<tr>
				  <td>Manager Name</td>
				  <td>'.$old_data['manager_name'].'</td>
				  <td>'.$new_data['manager_name'].'</td>
				</tr>
				<tr>
				  <td>Address</td>
				  <td>'.$old_data['address'].'</td>
				  <td>'.$new_data['address'].'</td>
				</tr>
				<tr>
				  <td>Phone</td>
				  <td>'.$old_data['phone'].'</td>
				  <td>'.$new_data['phone'].'</td>
				</tr>
				<tr>
				  <td>Email</td>
				  <td>'.$old_data['email'].'</td>
				  <td>'.$new_data['email'].'</td>
				</tr>
				<tr>
				  <td>Password</td>
				  <td>'.$old_data['password'].'</td>
				  <td>'.$new_data['password'].'</td>
				</tr>
				<tr>
				  <td>Logo</td>
				  <td>'.$old_data['logo'].'&nbsp;</td>
				  <td>'.$new_data['logo'].'&nbsp;</td>
				</tr>
			  </tbody>
			</table>';
		
		$subject = "Restaurant Profile Updated";
		
		$this->load->library('email');
		# Send email to Administrator
		$this->email->clear(TRUE);
		$this->email->set_mailtype("html");
		$this->email->from(SYSTEM_EMAIL, SYSTEM_NAME);
		$this->email->to(ADMIN_EMAIL);
		$this->email->subject($subject);
		$this->email->message(get_email_message_with_wrapper($html));
		$this->email->send();
		
	}
}


