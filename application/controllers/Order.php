<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Order extends CI_Controller 
{
	var $data;
	
	public function __construct()
	{
		parent::__construct();
		
		$this->load->model('login_model');
		$this->load->model('restaurant_model');
		$this->load->model('order_model');
		
		# if user is not logged in, then redirect him to login page
		if(! isset($_SESSION[USER_LOGIN]['id']) )
		{
			redirect('login');
		}
	}
	
	public function index()
	{
		$restaurant_id = $_SESSION[USER_LOGIN]['id'];
		$Msg = $this->restaurant_model->check_menu_staff($restaurant_id);
		if($Msg != '')
		{
			$_SESSION[USER_RETURN_MSG]['Msg']=$Msg;
			redirect(base_url());
		}
		reset_survey();
		$order_timestamp = time();
		
		$where_condition['menus.restaurant_id'] = $restaurant_id;
		$order_by['category_id'] = 'ASC';
		$order_by['menu_number'] = 'ASC';
		$this->data['menu_query'] = $this->restaurant_model->get_menus($where_condition, $order_by);
		$this->data['Table'] = $this->input->get_post('Table');
		$this->data['LoadCheckbox'] = 1;
		$this->data['order_timestamp'] = $order_timestamp;
		$this->data['Active'] = 'home';
		$this->load->view('take-order',$this->data);
	}
	
	public function take_order()
	{
		$restaurant_id = $_SESSION[USER_LOGIN]['id'];
		$MyItems = $this->input->get_post('selecteditems');
		$Url='';
		$action = '';
		$temporary_pending_orders_count = 0;
		$table_number = 0;
		
		$order_timestamp = $this->input->get_post('order_timestamp');
		$overwrite_table_number = $this->input->get_post('overwrite_table_number');
		$Table = $this->input->get_post('Table');
		$customerid = $this->input->get_post('customerid');
		$selecteditems = $this->input->get_post('selecteditems');
		$temporary = $this->input->get_post('temporary');
		$redirect_file = $this->input->get_post('redirect_file');
		$selecteditemsQuantity = $this->input->get_post('selecteditemsQuantity');
		$request_comment = $this->input->get_post('request_comment');
		
		if($overwrite_table_number > 0) // If user want to overwrite orders
		{
			$where_condition['DATE(date_created)'] = array("'".date('Y-m-d')."'",FALSE); // If you set it to FALSE, CodeIgniter will not try to protect your field or table names.
			$where_condition['order_timestamp !='] = $order_timestamp;
			$where_condition['review_done'] = 0;
			$where_condition['restaurant_id'] = $restaurant_id;
			$where_condition['table_number'] = $overwrite_table_number;
			$where_condition['deleted'] = 0;
			$where_condition['temporary'] = $temporary;
			// delete existing orders
			$this->order_model->update_orders($where_condition, array('deleted' => 1) );
		}
		if($Table=='')
		{
			$Error=1;
			$Msg='<div class="alert alert-error">Please select table</div>';
		}
		else if($customerid == '' or $customerid < 1)
		{
			$Error=1;
			$Msg='<div class="alert alert-error">Please enter customer id in the range of 1 to 100</div>';
		}
		else if(!is_array($selecteditems))
		{
			$Error=1;
			$Msg='<div class="alert alert-error">Please select order items</div>';
		}
		elseif($this->order_model->check_existing_order_on_same_table($restaurant_id,$Table,$temporary,$order_timestamp))
		{
			$Error=1;
			$action = 'hide confirm order button';
			$Msg="<div class='alert alert-warning'>There is already an existing order for Table $Table</div><div class='alert alert-warning'>Would you like to replace the existing one?</div>";
			$Msg.='<div><input type="hidden" name="overwrite_table_number" value="'.$Table.'" >
			<input type="submit" name="overwrite_button" id="overwrite_button" value="Yes Replace It" class="Button" style="background-color:#C30003;" />
			<input type="button" name="overwrite_cancel" id="overwrite_cancel" value="No" class="Button" style="background-color:#C30003;" /></div><br>';
		}
		else
		{
			
			$Error=0;
			$Msg='';
			$table_number = $Table;
			
			$insert_array = array();
			$insert_array['restaurant_id'] = $restaurant_id;
			$insert_array['table_number'] = $Table;
			$insert_array['customer_number'] = $customerid;
			$insert_array['temporary'] = $temporary;
			$insert_array['order_timestamp'] = $order_timestamp;
			
			$order_id = $this->order_model->add_order($insert_array);
			
			# count temporary orders
			if($temporary > 0)
			{
				$temporary_pending_orders_count = $this->order_model->count_temporary_pending_orders($restaurant_id,$Table);
			}
			
			$Count=0;
			foreach($MyItems as $ItemID)
			{
				$ItemID = str_replace("Item_", "", $ItemID);
				$Quantity = $selecteditemsQuantity[$Count];
				$req_comment = $request_comment[$ItemID];
				
				$insert_array = array();
				$insert_array['order_id'] = $order_id;
				$insert_array['menu_id'] = $ItemID;
				$insert_array['quantity'] = $Quantity;
				$insert_array['request_comment'] = $req_comment;
				$order_item_id = $this->order_model->add_order_item($insert_array);
				
				$Count++;
			}
			//$_SESSION[USER_RETURN_MSG]['Msg']='Order(s) saved successfully!';
			$Msg="Customer $customerid order saved";
			
			$Url=base_url().$redirect_file."?Table=$Table";
		}
		
		$Return["Error"]=$Error;
		$Return["Msg"]=$Msg;
		$Return["Url"]=$Url;
		$Return["action"] = $action;
		$Return['temporary_pending_orders_count'] = $temporary_pending_orders_count;
		$Return['table_number'] = $table_number;
		echo json_encode($Return);
	}
	
	public function existing_orders()
	{
		$restaurant_id = $_SESSION[USER_LOGIN]['id'];
		$Msg = $this->restaurant_model->check_menu_staff($restaurant_id);
		if($Msg != '')
		{
			$_SESSION[USER_RETURN_MSG]['Msg']=$Msg;
			redirect(base_url());
		}
		reset_survey();
		
		$where_condition['DATE(date_created) >='] = array("'".date('Y-m-d')."'",FALSE); // If you set it to FALSE, CodeIgniter will not try to protect your field or table names.
		$where_condition['review_done'] = 0;
		$where_condition['restaurant_id'] = $restaurant_id;
		$where_condition['deleted'] = 0;
		$where_condition['temporary'] = 0;
		$order_by['id'] = 'ASC';
		
		$this->data['orders_query'] = $this->order_model->get_orders($where_condition, $order_by);
		$this->data['Table'] = $this->input->get_post('Table');
		$this->data['LoadCheckbox'] = 0;
		$this->data['Active'] = 'home';
		$this->load->view('existing_orders',$this->data);
	}
	
	public function edit_order()
	{
		$restaurant_id = $_SESSION[USER_LOGIN]['id'];
		$order_id = $this->input->get_post('order_id');
		$Msg = $this->restaurant_model->check_menu_staff($restaurant_id);
		if($Msg != '')
		{
			$_SESSION[USER_RETURN_MSG]['Msg']=$Msg;
			redirect(base_url());
		}
		reset_survey();
		
		$customerid = $this->input->get_post('customerid');
		$Table = $this->input->get_post('Table');
		$selecteditems = $this->input->get_post('selecteditems');
		$selecteditemsQuantity = $this->input->get_post('selecteditemsQuantity');
		$request_comment = $this->input->get_post('request_comment');
		
		if($_POST)
		{
			$MyItems = $this->input->get_post('Items');
	
			if($Table=='')
			{
				$_SESSION['msg_error'][] = 'Please select table';
			}
			
			if($customerid == '' or $customerid < 1)
			{
				$_SESSION['msg_error'][] = 'Please enter customer id in the range of 1 to 100';
			}
			
			if(!is_array($selecteditems))
			{
				$_SESSION['msg_error'][] = 'Please select order items';
			}
			
			if( ! isset($_SESSION['msg_error']) )
			{
				$this->order_model->update_order($order_id, array('table_number' => $Table,'customer_number' => $customerid) );
				
				# Delete previous all records for this order
				$this->order_model->delete_all_items_of_this_order($order_id);
				
				$Count=0;
				foreach($MyItems as $ItemID)
				{
					$Quantity = $selecteditemsQuantity[$Count];
					$req_comment = $request_comment[$ItemID];
					
					$insert_array = array();
					$insert_array['order_id'] = $order_id;
					$insert_array['menu_id'] = $ItemID;
					$insert_array['quantity'] = $Quantity;
					$insert_array['request_comment'] = $req_comment;
					$order_item_id = $this->order_model->add_order_item($insert_array);
					
					$Count++;
				}
				$_SESSION['msg_success'][] = 'Order updated successfully!';
				
				redirect('order/existing_orders');
				exit;
			}
			
			redirect("order/edit_order/?order_id=$order_id");
			exit;
		}
		
		
		
		$order_items_query = $this->order_model->get_order_items($order_id);
		$items = array();
		foreach($order_items_query->result() as $row)
		{
			$items[$row->menu_id] = $row;
		}
		
		$where_condition['menus.restaurant_id'] = $restaurant_id;
		$order_by['category_id'] = 'ASC';
		$order_by['menu_number'] = 'ASC';
		$this->data['menu_query'] = $this->restaurant_model->get_menus($where_condition, $order_by);
		$this->data['order_id'] = $order_id;
		$this->data['order'] = $this->order_model->get_order_by_id($order_id);
		$this->data['order_items_query'] = $order_items_query;
		$this->data['items'] = $items;
		$this->data['LoadCheckbox'] = 1;
		$this->data['Active'] = 'home';
		$this->load->view('edit-order',$this->data);
	}
	public function menu_list()
	{
		$restaurant_id = $_SESSION[USER_LOGIN]['id'];
		$Msg = $this->restaurant_model->check_menu_staff($restaurant_id);
		if($Msg != '')
		{
			$_SESSION[USER_RETURN_MSG]['Msg']=$Msg;
			redirect(base_url());
		}
		reset_survey();
		$order_timestamp = time();
		
		# Delete all existing temporary pending orders of today date
		$this->order_model->delete_all_temporary_pending_orders($restaurant_id);
		
		$where_condition['menus.restaurant_id'] = $restaurant_id;
		$order_by['category_id'] = 'ASC';
		$order_by['menu_number'] = 'ASC';
		
		$this->data['menu_query'] = $this->restaurant_model->get_menus($where_condition, $order_by);
		$this->data['Table'] = $this->input->get_post('Table');
		$this->data['LoadCheckbox'] = 1;
		$this->data['order_timestamp'] = $order_timestamp;
		$this->data['Active'] = 'home';
		$this->load->view('menu_list',$this->data);
	}
	
	# Check and count temporary pending orders for feedback
	public function count_temporary_pending_orders()
	{
		$table_number = $this->input->get_post('table_number');
		$restaurant_id = $_SESSION[USER_LOGIN]['id'];
		
		$return['total_orders_pending'] = $this->order_model->count_temporary_pending_orders($restaurant_id,$table_number);
		$return["Error"]=0;
		$return["Msg"]='';
		echo json_encode($return);
	}
	
	public function make_feedback_for_single_table()
	{
		$restaurant_id = $_SESSION[USER_LOGIN]['id'];
		
		$Table = $this->input->get_post('Table');
		$temporary = $this->input->get_post('temporary');
		
		if($Table=='')
		{
			$Error=1;
			$Msg='<div class="alert alert-error">Please select table</div>';
		}
		else
		{
			$where_condition['DATE(date_created)'] = array("'".date('Y-m-d')."'",FALSE); // If you set it to FALSE, CodeIgniter will not try to protect your field or table names.
			$where_condition['review_done'] = 0;
			$where_condition['restaurant_id'] = $restaurant_id;
			$where_condition['table_number'] = $Table;
			$where_condition['deleted'] = 0;
			$where_condition['temporary'] = $temporary;
			$order_by['customer_number'] = 'ASC';
			
			$orders_query = $this->order_model->get_orders($where_condition, $order_by);
			
			$total_orders = $orders_query->num_rows();
			if($total_orders == 0)
			{
				$Error=1;
				$Msg='<div class="alert alert-error">No recent order found for this table</div>';
			}
			else
			{
				reset_survey();
				$_SESSION[SURVEY_COUNT_TABLE]=$total_orders;
				$_SESSION[SURVEY_LOOP]	= $total_orders;
				$_SESSION[SURVEY_TABLE]	= $Table;
				
				$i = 0;
				foreach($orders_query->result() as $row)
				{
					$i++;
					$order_id = $row->id;
					$customer_number = $row->customer_number;
					
					$orders[$i] = $row;
					
					$orders_items_query = $this->order_model->get_order_items($order_id);
					
					$Items = array();
					foreach($orders_items_query->result() as $row2)
					{
						$Items[] = $row2->menu_id;
					}
					
					$_SESSION[SURVEY_ITEMS][$i]	= $Items;
				}
				$_SESSION[SURVEY_ORDER] = $orders;
				$Error=0;
				$Msg = 'Order is ready for feedback.';
			}
		}
		
		$Return["Error"]=$Error;
		$Return["Msg"]=$Msg;
		echo json_encode($Return);
	}
	
	public function make_feedback_for_single_order()
	{
		$restaurant_id = $_SESSION[USER_LOGIN]['id'];
		
		$order_id = $this->input->get_post('SelectedOrder');
		$temporary = $this->input->get_post('temporary');
		
		$order_row = $this->order_model->get_order_by_id($order_id);
		
		$customer_number = $order_row->customer_number;
		$Table = $order_row->table_number;
		
		$orders_items_query = $this->order_model->get_order_items($order_id);
		foreach($orders_items_query->result() as $row2)
		{
			$Items[] = $row2->menu_id;
		}
		
		reset_survey();
		$_SESSION[SURVEY_COUNT_TABLE]=1;
		$_SESSION[SURVEY_ORDER]	= array('1'=> $order_row);
		$_SESSION[SURVEY_LOOP]	= 1;
		$_SESSION[SURVEY_TABLE]	= $Table;
		$_SESSION[SURVEY_ITEMS][1]	= $Items;
		$Error=0;
		$Msg = 'Order is ready for feedback.';
		
		$Return["Error"]=$Error;
		$Return["Msg"]=$Msg;
		echo json_encode($Return);
	}
	
	public function start_feedback()
	{
		$restaurant_id = $_SESSION[USER_LOGIN]['id'];
		if(!isset($_SESSION[USER_LOGIN]) or !isset($_SESSION[SURVEY_LOOP]) or !isset($_SESSION[SURVEY_TABLE]) or !isset($_SESSION[SURVEY_ITEMS]))
		{
			redirect(base_url());
		}
		
		if(!isset($_SESSION[CURRENT_SURVEY])) $_SESSION[CURRENT_SURVEY]=1;
		
		$CurrentSurvey = $_SESSION[CURRENT_SURVEY];
		
		$this->data['customerid'] = isset($_SESSION['SurveyOrder'][$CurrentSurvey]->customer_number) ? $_SESSION['SurveyOrder'][$CurrentSurvey]->customer_number : $CurrentSurvey ;
		
		$items = implode(",", $_SESSION[SURVEY_ITEMS][$CurrentSurvey]);
		
		$where_condition['menus.id IN '] = array("($items)",false);
		$order_by['category_id'] = 'ASC';
		$order_by['menu_number'] = 'ASC';
		
		$this->data['menu_query'] = $this->restaurant_model->get_menus($where_condition, $order_by);
		$this->data['staff_query'] = $this->restaurant_model->get_staffs($restaurant_id);
		//my_var_dump($_REQUEST);
		//my_var_dump($_SESSION);
		
		$this->data['Active'] = 'menu';
		$this->load->view('start-feedback',$this->data);
	}
	
	public function submit_feedback()
	{
		$restaurant_id = $_SESSION[USER_LOGIN]['id'];
		$rates = $this->input->get_post('rates');
		$comments = $this->input->get_post('comments');
		$Region = $this->input->get_post('Region') ? $this->input->get_post('Region') : $this->input->get_post('region_text');
		$HearAboutUs = $this->input->get_post('HearAboutUs') ? $this->input->get_post('HearAboutUs') : $this->input->get_post('hear_about_text');
		$customerExp = $this->input->get_post('customerExp');
		$OrderSpeed = $this->input->get_post('OrderSpeed');
		$ComeAgain = $this->input->get_post('ComeAgain');
		$Member = $this->input->get_post('Member');
		$suggestion = $this->input->get_post('suggestion');
		$Email = $this->input->get_post('Email');
		$focus_element = '';
		
		$negative_feedback = false; // set false by default
		
		if($ComeAgain == 'No') // 2 means NO, Customer will not come again
		{
			$negative_feedback = true; 
		}
		
		if(!isset($_SESSION[CURRENT_SURVEY])) $_SESSION[CURRENT_SURVEY]=1;
		
		$CurrentSurvey = $_SESSION[CURRENT_SURVEY];
		
		$items = $_SESSION[SURVEY_ITEMS][$CurrentSurvey];
				
		$Count=0;
		foreach($items as $item)
		{
			$UserRate = $rates[$Count];
			if($UserRate<=0)
			{
				$itemRateError=1;
				$Msg = '<div class="alert alert-error">Please enter ratings of items</div>';
				break;
			}
			elseif($UserRate < 3) // if true, its mean negative feedback provided by customer
			{
				$negative_feedback = true;
			}
			$Count++;
		}
		if(@$itemRateError==1)
		{
			$Error=1;
			$Msg = '<div class="alert alert-error">Please rate your menu items.</div>';
			$focus_element = 'menu_items_error_div';
		}
		else
		{
			if($customerExp<=0)
			{
				$Error=1;
				$Msg = '<div class="alert alert-error">Please rate your customer experience.</div>';
				$focus_element = 'customer_experience_error_div';
			}
			else if($OrderSpeed<=0)
			{
				$Error=1;
				$Msg = '<div class="alert alert-error">Please rate the speed of your order</div>';
				$focus_element = 'speed_order_error_div';
			}
			else if($Member=='')
			{
				$Error=1;
				$Msg = '<div class="alert alert-error">Please select staff member who stood out</div>';
			}
			else if($Region=='')
			{
				$Error=1;
				$Msg = '<div class="alert alert-error">Please select your region.</div>';
			}
			else if($HearAboutUs=='')
			{
				$Error=1;
				$Msg = '<div class="alert alert-error">Please select how you heard about us</div>';
			}
			else
			{
				if($customerExp < 3 or $OrderSpeed < 3) // if true, its mean negative feedback provided by customer
				{
					$negative_feedback = true;
				}
				
				$Error=0;
				$order_id = isset($_SESSION[SURVEY_ORDER]) ? $_SESSION[SURVEY_ORDER][$CurrentSurvey]->id : 0;
				
				$this->order_model->update_order($order_id, array('review_done' => 1,'order_done' => 1) );
				
				$insert_data = array();
				$insert_data['restaurant_id'] = $restaurant_id;
				$insert_data['order_id'] = $order_id;
				$insert_data['table_number'] = $_SESSION[SURVEY_TABLE];
				$insert_data['email'] = $Email;
				$insert_data['region'] = ucfirst($Region);
				$insert_data['hear_about_us'] = ucfirst($HearAboutUs);
				$insert_data['customer_experience'] = $customerExp;
				$insert_data['order_speed'] = $OrderSpeed;
				$insert_data['come_again'] = $ComeAgain;
				$insert_data['staff_id'] = $Member;
				$insert_data['suggestion'] = ucfirst($suggestion);
				$insert_data['restaurant_id'] = $restaurant_id;
				
				$rating_id = $this->order_model->add_rating($insert_data);
				
				$Count=0;
				foreach($items as $item)
				{
					$UserRate = $rates[$Count];
					$Comment = $comments[$Count];
					
					$insert_data = array();
					$insert_data['rating_id'] = $rating_id;
					$insert_data['menu_id'] = $item;
					$insert_data['rate'] = $UserRate;
					$insert_data['item_comment'] = $Comment;
					
					$rating_item_id = $this->order_model->add_rating_item($insert_data);
					
					$Count++;
				}
				//$_SESSION[SURVEY_LOOP] = $_SESSION[SURVEY_LOOP] - 1;
				
				if($negative_feedback) // if Negative Feedback
				{
					$Table = $_SESSION[SURVEY_TABLE];
					// Send negative feedback report to Restaurant Owner $_SESSION[USER_LOGIN]['Email']
					$to = $_SESSION[USER_LOGIN]['email'];
					$Subject = 'Negative Feedback Alert';
					ob_start();
					
					$this->data['rating_id'] = $rating_id;
					$this->load->view('email-feedback',$this->data);
					
					$message = ob_get_contents();
					ob_end_clean();
					
					# Send email to Signup User
					$this->email->clear(TRUE);
					$this->email->set_mailtype("html");
					$this->email->from(SYSTEM_EMAIL, SYSTEM_NAME);
					$this->email->to($to);
					$this->email->subject($Subject);
					$this->email->message(get_email_message_with_wrapper($message));
					$this->email->send();
				}
				
				if($_SESSION[CURRENT_SURVEY]==$_SESSION[SURVEY_LOOP])
				{	unset($_SESSION['GoBackToFeedback']); }
				else
				{
					$_SESSION[CURRENT_SURVEY]++;
					$_SESSION['GoBackToFeedback']=1;
					$_SESSION[USER_RETURN_MSG]['Msg']='Reviews saved successfully.';
				}
			}
		}
		$Return["Error"]		= $Error;
		$Return["Msg"]			= @$Msg;
		$Return["NextPerson"]	= @$NextPerson;
		$Return['focus_element'] = $focus_element;
		echo json_encode($Return);

		
	}
	
	public function delete_selected_orders()
	{
		$selected_ids = $this->input->get_post('selected_ids');
		if( is_array($selected_ids) and count($selected_ids) )
		{
			foreach($selected_ids as $order_id)
			{
				if($this->input->get_post('hard_delete') == 1)
				{
					$this->order_model->delete_order($order_id);
				}
				else
				{
					$this->order_model->update_order($order_id, array('deleted' => 1) );
				}
			}
			$_SESSION['msg_error'][] = count($selected_ids)." Record deleted...";
		}
		else
		{
			$_SESSION['msg_error'][] = "Please select some checkboxes";
		}
		
		exit;
	}
}




