<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Restaurant extends CI_Controller 
{
	var $data;
	
	public function __construct()
	{
		parent::__construct();
		
		$this->load->model('login_model');
		$this->load->model('restaurant_model');
		
		# if user is not logged in, then redirect him to login page
		if(! isset($_SESSION[USER_LOGIN]['id']) )
		{
			redirect('login');
		}
	}
	
	public function index()
	{
		my_var_dump(__FILE__);
	}
	
	public function staff()
	{
		$restaurant_id = $_SESSION[USER_LOGIN]['id'];
		$this->data['ActionType']='AddStaff';
		$this->data['MenuBtn']='Add Staff';
		
		$id = $this->input->get_post('id');
		$this->data['id'] = $id;
		
		if($this->input->get_post('ActionType') == 'Edit' and $id > 0)
		{
			$staff = $this->restaurant_model->get_staff_by_id($id);
			$this->data['staff'] = $staff;
			
			$this->data['ActionType'] = 'EditStaff';
			$this->data['MenuBtn'] = 'Update Staff';
		}
		
		// Set the validation rules
		$this->form_validation->set_rules('title', 'Staff Member Name', 'required|trim');
		$this->form_validation->set_rules('designation', 'Role or Designation', 'required|trim');
		
		// If the validation worked
		if ($this->form_validation->run())
		{
			if($this->input->get_post('ActionType') == 'Edit' and $id > 0)
			{
				$update_array['title'] = $this->input->get_post('title');
				$update_array['designation'] = $this->input->get_post('designation');
				
				$this->restaurant_model->update_staff($id,$update_array);
				$_SESSION[USER_RETURN_MSG]['Msg'] = 'Staff updated successfully!';
			}
			else
			{
				$insert_array['restaurant_id'] = $restaurant_id;
				$insert_array['title'] = $this->input->get_post('title');
				$insert_array['designation'] = $this->input->get_post('designation');
				
				$id = $this->restaurant_model->add_staff($insert_array);
				$_SESSION[USER_RETURN_MSG]['Msg'] = 'Staff added successfully!';
			}
			
			$oldfile = $this->input->get_post('oldfile');
			if($this->input->get_post('delete_old'))
			{
				# Delete old file if there was any
				if(delete_file('./'.UPLOADS.'/'.$oldfile))
				{
					$_SESSION['msg_error'][] = " $oldfile file deleted. ";
					$this->restaurant_model->update_staff($id,array('image'=>''));
				}
			}
			
			# File uploading configuration
			$config['upload_path'] = './'.UPLOADS.'/';
			$config['allowed_types'] = 'gif|jpg|png|jpeg';
			$config['encrypt_name'] = true;
	
			$this->load->library('upload', $config);
			
			# Try to upload file now
			if ($this->upload->do_upload('image'))
			{
				# Get uploading detail here
				$upload_detail = $this->upload->data();
				
				# Get file name
				$image = $upload_detail['file_name'];
				
				# Delete old file if there was any
				if(delete_file('./'.UPLOADS.'/'.$oldfile))
				{
					$_SESSION['msg_error'][] = " $oldfile file deleted. ";
					$this->restaurant_model->update_staff($id,array('image'=>''));
				}
				
				# Save uploaded file in database
				$this->restaurant_model->update_staff($id,array('image'=>$image));
				
				# Get width and height of uploaded file
				$image_path = './'.UPLOADS.'/'.$image;
				$width = get_width($image_path);
				$height = get_height($image_path);
				
				# Resize Image Now
				$width > RESIZE_IF_PIXELS_LIMIT ? resize_image2($image_path, RESIZE_IMAGE_WIDTH, '', 'W') : '';
				$height > RESIZE_IF_PIXELS_LIMIT ? resize_image2($image_path, '', RESIZE_IMAGE_HEIGHT, 'H') : '';
				
			}
			else
			{
				$uploaded_file_array = (isset($_FILES['image']) and $_FILES['image']['size'] > 0 and $_FILES['image']['error'] == 0) ? $_FILES['image'] : '';
				# Show uploading error only when the file uploading attempt exist.
				if( is_array($uploaded_file_array) )
				{
					$uploading_error = $this->upload->display_errors();
					$_SESSION['msg_error'][] = $uploading_error;
				}
			}
			
			redirect('/restaurant/staff');
			exit;
		}
		
		$this->data['staff_query'] = $this->restaurant_model->get_staffs($restaurant_id);
		$this->data['Active'] = 'staff';
		$this->load->view('staff',$this->data);
	}
	
	public function delete_category($category_id)
	{
		$Return['Error']='1';
		$Return['Msg']='Wrong request.';
		if($category_id > 0)
		{
			$this->restaurant_model->delete_category($category_id);
				
			$_SESSION[USER_RETURN_MSG]['Msg']='Category deleted successfully';
			$Return['Error']=0;
		}
		echo json_encode($Return);
	}
	
	public function delete_staff($id)
	{
		$Return['Error']='1';
		$Return['Msg']='Wrong request.';
				
		if($id > 0)
		{
			$this->restaurant_model->delete_staff($id);
			
			$Return['Error']='0';
			$_SESSION[USER_RETURN_MSG]['Msg']='Staff deleted successfully';
			$Return['Msg']='Staff deleted successfully';
		}
		echo json_encode($Return);
	}
	
	public function delete_menu($id)
	{
		$Return['Error']='1';
		$Return['Msg']='Wrong request.';
				
		if($id > 0)
		{
			$this->restaurant_model->delete_menu($id);
			
			$Return['Error']='0';
			$_SESSION[USER_RETURN_MSG]['Msg']='Item deleted successfully';
			$Return['Msg']='Item deleted successfully';
		}
		echo json_encode($Return);
	}
	
	public function menu()
	{
		$restaurant_id = $_SESSION[USER_LOGIN]['id'];
		$this->data['ActionType']='AddMenu';
		$this->data['MenuBtn']='Add Item';
		
		$this->data['categories'] = $this->restaurant_model->get_categories($restaurant_id);
		
		$id = $this->input->get_post('id');
		$this->data['id'] = $id;
		
		if($this->input->get_post('ActionType') == 'Edit' and $id > 0)
		{
			$menu = $this->restaurant_model->get_menu_by_id($id);
			$this->data['menu'] = $menu;
			
			$this->data['ActionType'] = 'EditMenu';
			$this->data['MenuBtn'] = 'Update Item';
		}
		
		// Set the validation rules
		if($this->input->get_post('category_id') === NULL)
		{
			$this->form_validation->set_rules('new_category', 'New Category', 'required|trim');
		}
		else
		{
			$this->form_validation->set_rules('category_id', 'Category', 'required|numeric|trim');
		}
		$this->form_validation->set_rules('title', 'Item Name', 'required|trim');
		$this->form_validation->set_rules('description', 'Description', 'trim');
		//$this->form_validation->set_rules('price', 'Price', 'required|trim|numeric');
		//$this->form_validation->set_rules('menu_number', 'Menu Number', 'required|trim|numeric');
		
		// If the validation worked
		if ($this->form_validation->run())
		{
			if($this->input->get_post('new_category') != '')
			{
				$cat_array['restaurant_id'] = $restaurant_id;
				$cat_array['title'] = $this->input->get_post('new_category');
				$category_id = $this->restaurant_model->add_category($cat_array);
			}
			else
			{
				$category_id = $this->input->get_post('category_id');
			}
			
			if($this->input->get_post('ActionType') == 'Edit' and $id > 0)
			{
				$update_array['category_id'] = $category_id;
				$update_array['title'] = $this->input->get_post('title');
				$update_array['price'] = $this->input->get_post('price');
				$update_array['menu_number'] = $this->input->get_post('menu_number')==''?10000:$this->input->get_post('menu_number');
				$update_array['description'] = $this->input->get_post('description');
				$update_array['popular'] = $this->input->get_post('popular')!='' ? $this->input->get_post('popular') : 0;
				
				$this->restaurant_model->update_menu($id,$update_array);
				$_SESSION[USER_RETURN_MSG]['Msg'] = 'Item updated successfully!';
			}
			else
			{
				$insert_array['restaurant_id'] = $restaurant_id;
				$insert_array['category_id'] = $category_id;
				$insert_array['title'] = $this->input->get_post('title');
				$insert_array['price'] = $this->input->get_post('price');
				$insert_array['menu_number'] = $this->input->get_post('menu_number')==''?10000:$this->input->get_post('menu_number');
				$insert_array['description'] = $this->input->get_post('description');
				$insert_array['popular'] = $this->input->get_post('popular')!='' ? $this->input->get_post('popular') : 0;
				
				$id = $this->restaurant_model->add_menu($insert_array);
				$_SESSION[USER_RETURN_MSG]['Msg'] = 'Item added successfully!';
			}
			
			$oldfile = $this->input->get_post('oldfile');
			if($this->input->get_post('delete_old'))
			{
				# Delete old file if there was any
				if(delete_file('./'.UPLOADS.'/'.$oldfile))
				{
					$_SESSION['msg_error'][] = " $oldfile file deleted. ";
					$this->restaurant_model->update_menu($id,array('image'=>''));
				}
			}
			
			# File uploading configuration
			$config['upload_path'] = './'.UPLOADS.'/';
			$config['allowed_types'] = 'gif|jpg|png|jpeg';
			$config['encrypt_name'] = true;
	
			$this->load->library('upload', $config);
			
			# Try to upload file now
			if ($this->upload->do_upload('image'))
			{
				# Get uploading detail here
				$upload_detail = $this->upload->data();
				
				# Get file name
				$image = $upload_detail['file_name'];
				
				# Delete old file if there was any
				if(delete_file('./'.UPLOADS.'/'.$oldfile))
				{
					$_SESSION['msg_error'][] = " $oldfile file deleted. ";
					$this->restaurant_model->update_menu($id,array('image'=>''));
				}
				
				# Save uploaded file in database
				$this->restaurant_model->update_menu($id,array('image'=>$image));
				
				# Get width and height of uploaded file
				$image_path = './'.UPLOADS.'/'.$image;
				$width = get_width($image_path);
				$height = get_height($image_path);
				
				# Resize Image Now
				$width > RESIZE_IF_PIXELS_LIMIT ? resize_image2($image_path, RESIZE_IMAGE_WIDTH, '', 'W') : '';
				$height > RESIZE_IF_PIXELS_LIMIT ? resize_image2($image_path, '', RESIZE_IMAGE_HEIGHT, 'H') : '';
				
			}
			else
			{
				$uploaded_file_array = (isset($_FILES['image']) and $_FILES['image']['size'] > 0 and $_FILES['image']['error'] == 0) ? $_FILES['image'] : '';
				# Show uploading error only when the file uploading attempt exist.
				if( is_array($uploaded_file_array) )
				{
					$uploading_error = $this->upload->display_errors();
					$_SESSION['msg_error'][] = $uploading_error;
				}
			}
			
			redirect('/restaurant/menu');
			exit;
		}
		
		$where_condition['menus.restaurant_id'] = $restaurant_id;
		$order_by['category_id'] = 'ASC';
		$order_by['menu_number'] = 'ASC';
		$this->data['menu_query'] = $this->restaurant_model->get_menus($where_condition, $order_by);
		$this->data['Active'] = 'menu';
		$this->load->view('menu',$this->data);
	}
	
	public function profile()
	{
		$Msg = '';
		$Error = 0;
		$restaurant_id = $_SESSION[USER_LOGIN]['id'];
		$Delete1 = $this->input->get_post('Delete1');
		$oldfile = $this->input->get_post('oldfile');
		
		$old_data = $this->login_model->get_user_by_id($restaurant_id);
		$new_data['business_type'] = $this->input->get_post('business_type');
		$new_data['ordering_feature'] = $this->input->get_post('ordering_feature');
		$new_data['name'] = $this->input->get_post('name');
		$new_data['manager_name'] = $this->input->get_post('manager_name');
		$new_data['address'] = $this->input->get_post('address');
		$new_data['phone'] = $this->input->get_post('phone');
		$new_data['email'] = $this->input->get_post('email');
		$new_data['password'] = $this->input->get_post('password');
		$new_data['logo'] = 'Not Uploaded';
		
		//	DELETE PHOTO 1
		if($Delete1)
		{
			if(delete_file('./'.UPLOADS.'/'.$oldfile))
			{
				$Msg .= " $oldfile file deleted. ";
				$this->login_model->update_user($restaurant_id,array('logo'=>''));
				$old_data['logo'] = 'Deleted';
			}
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
		$this->form_validation->set_rules('password', 'Password', 'min_length[8]|max_length[20]|matches[confirm_password]|md5');
		$this->form_validation->set_rules('confirm_password', 'Password Confirmation', 'trim');
		
		# If the validation is passed
		if ($this->form_validation->run())
		{
			# Try to upload file now
			if ($this->upload->do_upload('logo'))
			{
				# Get uploading detail here
				$upload_detail = $this->upload->data();
				
				# Get file name
				$logo = $upload_detail['file_name'];
				$new_data['logo'] = $logo;
				
				# Delete old file if there was any
				if(delete_file('./'.UPLOADS.'/'.$oldfile))
				{
					$Msg .= " $oldfile file deleted. ";
					$this->login_model->update_user($restaurant_id,array('logo'=>''));
					$old_data['logo'] = 'Deleted';
				}
				
				# Save uploaded file in database
				$this->login_model->update_user($restaurant_id,array('logo'=>$logo));
				
				# Get width and height of uploaded file
				$image_path = './'.UPLOADS.'/'.$logo;
				$width = get_width($image_path);
				$height = get_height($image_path);
				
				if(strtolower($upload_detail['file_ext']) != '.png')
				{
					# Resize Image Now
					$width > RESIZE_IF_PIXELS_LIMIT ? resize_image2($image_path, RESIZE_IMAGE_WIDTH, '', 'W') : '';
					$height > RESIZE_IF_PIXELS_LIMIT ? resize_image2($image_path, '', RESIZE_IMAGE_HEIGHT, 'H') : '';
				}
			}
			else
			{
				$uploaded_file_array = (isset($_FILES['logo']) and $_FILES['logo']['size'] > 0 and $_FILES['logo']['error'] == 0) ? $_FILES['logo'] : "";
				# Show uploading error only when the file uploading attempt exisit.
				if( is_array($uploaded_file_array) )
				{
					$Error = 1;
					$uploading_error = $this->upload->display_errors();
					//$_SESSION['msg_error'][] = $uploading_error;
					$Msg .= $uploading_error;
				}
			}
			
			$update_array['business_type'] = $new_data['business_type'];
			$update_array['ordering_feature'] = $new_data['ordering_feature'];
			$update_array['name'] = $new_data['name'];
			$update_array['manager_name'] = $new_data['manager_name'];
			$update_array['address'] = $new_data['address'];
			$update_array['phone'] = $new_data['phone'];
			if($password!='')
			{
				$update_array['password'] = md5($new_data['password']);
			}
			# Update data into database
			$this->login_model->update_user($restaurant_id,$update_array);
			
			$Msg .= 'Profile Updated Successfully. ';
			
			# Send email to admin about profile changes.
			$this->login_model->email_admin_profile_changes($old_data,$new_data);
			
			# Update session profile data
			$_SESSION[USER_LOGIN] = $this->login_model->get_user_by_id($restaurant_id);
			
			$_SESSION[USER_RETURN_MSG]['Error'] = $Error;
			$_SESSION[USER_RETURN_MSG]['Msg'] = $Msg;
			
			redirect('/restaurant/profile/','refresh');
			exit;
		}
		
		$this->data['profile'] = $old_data;
		$this->data['Active'] = 'profile';
		$this->load->view('profile',$this->data);
	}
	
	
}




