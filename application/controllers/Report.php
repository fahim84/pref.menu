<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Report extends CI_Controller 
{
	var $data;
	
	public function __construct()
	{
		parent::__construct();
		
		$this->load->model('login_model');
		$this->load->model('restaurant_model');
		$this->load->model('order_model');
		$this->load->model('report_model');
		
		//load our new PHPExcel library
		$this->load->library('excel');
		
		# if user is not logged in, then redirect him to login page
		if(! isset($_SESSION[USER_LOGIN]['id']) )
		{
			redirect('login');
		}
	}
	
	public function index()
	{
		$restaurant_id = $_SESSION[USER_LOGIN]['id'];
		$join_date = $_SESSION[USER_LOGIN]['date_created'];
		$graph_interval = $this->input->get_post('graph_interval') != '' ? $this->input->get_post('graph_interval') : 'Daily';
		
		$start_date = $this->input->get_post('start_date') != '' ? $this->input->get_post('start_date') : $join_date;
		$end_date = $this->input->get_post('end_date') != '' ? $this->input->get_post('end_date') : date('Y-m-d');
		
		$this->data['start_date'] = date('Y-m-d',strtotime($start_date));
		$this->data['end_date'] = date('Y-m-d',strtotime($end_date));
		$this->data['graph_interval'] = $graph_interval;
		
		# Get all menu items
		$where_condition['menus.restaurant_id'] = $restaurant_id;
		$order_by['category_id'] = 'ASC';
		$order_by['menu_number'] = 'ASC';
		$this->data['menu_query'] = $this->restaurant_model->get_menus($where_condition, $order_by);
		
		# Count Feedbacks
		$count_feedbacks = $this->report_model->count_feedbacks($restaurant_id,$start_date,$end_date);
		$this->data['count_feedbacks'] = $count_feedbacks;
		
		# Get top staff member
		$this->data['top_staff_member'] = $this->report_model->get_top_staff_member($restaurant_id,$start_date,$end_date);
		
		if($count_feedbacks > 4)
		{
			# Get top and low rated item
			$top_low_item = $this->report_model->get_top_and_low_menu($restaurant_id,$start_date,$end_date);
			$top_item = $top_low_item[0];
			$low_item = $top_low_item[1];
			$this->data['top_item'] = $top_item;
			$this->data['low_item'] = $low_item;
		}
		
		$this->data['Active'] = 'Reports';
		$this->load->view('report',$this->data);
	}
	
	public function menu_ratings()
	{
		$restaurant_id = $_SESSION[USER_LOGIN]['id'];
		$join_date = $_SESSION[USER_LOGIN]['date_created'];
		$graph_interval = $this->input->get_post('graph_interval') != '' ? $this->input->get_post('graph_interval') : 'Daily';
		
		$start_date = $this->input->get_post('start_date') != '' ? $this->input->get_post('start_date') : $join_date;
		$end_date = $this->input->get_post('end_date') != '' ? $this->input->get_post('end_date') : date('Y-m-d');
		
		$this->data['start_date'] = date('Y-m-d',strtotime($start_date));
		$this->data['end_date'] = date('Y-m-d',strtotime($end_date));
		$this->data['graph_interval'] = $graph_interval;
		
		# Get all menu items
		$where_condition['menus.restaurant_id'] = $restaurant_id;
		$order_by['category_id'] = 'ASC';
		$order_by['menu_number'] = 'ASC';
		$this->data['menu_query'] = $this->restaurant_model->get_menus($where_condition, $order_by);
		
		# Count Feedbacks
		$count_feedbacks = $this->report_model->count_feedbacks($restaurant_id,$start_date,$end_date);
		$this->data['count_feedbacks'] = $count_feedbacks;
		
		$this->data['Active'] = 'menu-ratings';
		$this->load->view('menu-ratings',$this->data);
	}
	
	public function review_report()
	{
		$restaurant_id = $_SESSION[USER_LOGIN]['id'];
		$join_date = $_SESSION[USER_LOGIN]['date_created'];
		
		$start_date = $this->input->get_post('start_date') != '' ? $this->input->get_post('start_date') : $join_date;
		$end_date = $this->input->get_post('end_date') != '' ? $this->input->get_post('end_date') : date('Y-m-d');
		
		$this->data['start_date'] = date('Y-m-d',strtotime($start_date));
		$this->data['end_date'] = date('Y-m-d',strtotime($end_date));
		
		# Get rating
		$where_condition['restaurant_id'] = $restaurant_id;
		$where_condition['DATE(date_created) BETWEEN '] = array("DATE('$start_date') AND DATE('$end_date')",FALSE); // If you set it to FALSE, CodeIgniter will not try to protect your field or table names.
		$order_by['id'] = 'DESC';
		$this->data['rating_query'] = $this->report_model->get_ratings($where_condition, $order_by);
		
		$this->data['Active'] = 'Review';
		$this->load->view('review-report',$this->data);
	}
	
	public function change_password()
	{
		$restaurant_id = $_SESSION[USER_LOGIN]['id'];
		$AccountPassword = $this->input->get_post('AccountPassword');
		$ReportPassword = $this->input->get_post('ReportPassword');
		$change_password = $this->input->get_post('change_password');
		
		if($_POST)
		{
			if($change_password == 'account')
			{
				$this->login_model->update_user($restaurant_id,array('password' => md5($AccountPassword)));
				$_SESSION[USER_LOGIN]['password']=md5($AccountPassword);
				$Return['Error']=0;
				$Return['Msg']='<div class="alert alert-success">Account password changed successfully!</div>';
				echo json_encode($Return);
			}
			if($change_password == 'report')
			{
				$this->login_model->update_user($restaurant_id,array('report_password' => md5($ReportPassword)));
				$_SESSION[USER_LOGIN]['report_password']=md5($ReportPassword);
				$Return['Error']=0;
				$Return['Msg']='<div class="alert alert-success">Report password changed successfully!</div>';
				echo json_encode($Return);
			}
			exit;
		}
		$this->data['Active'] = 'Password';
		$this->load->view('change-password',$this->data);
	}
	
	public function deleted_orders()
	{
		$restaurant_id = $_SESSION[USER_LOGIN]['id'];
		
		# Mark all orders as deleted which were older than 24 hours and left without feedback.
		$where_condition['date_created <'] = array('DATE_SUB(SYSDATE(), INTERVAL 24 HOUR)',FALSE); // If you set it to FALSE, CodeIgniter will not try to protect your field or table names.
		$where_condition['review_done'] = 0;
		$where_condition['order_done'] = 0;
		$where_condition['deleted'] = 0;
		$this->order_model->update_orders($where_condition, array('deleted' => 1) );
		unset($where_condition);
		
		# Get Deleted orders now
		$where_condition['restaurant_id'] = $restaurant_id;
		$where_condition['deleted'] = 1;
		$order_by['id'] = 'DESC';
		$this->data['orders_query'] = $this->order_model->get_orders($where_condition, $order_by);
			
		$this->data['Active'] = 'deleted-orders';
		$this->load->view('deleted-orders',$this->data);
	}
	
	public function download_report()
	{
		$restaurant_id = $_SESSION[USER_LOGIN]['id'];
		$join_date = $_SESSION[USER_LOGIN]['date_created'];
		
		$start_date = $this->input->get_post('start_date') != '' ? $this->input->get_post('start_date') : date('Y-m-d',strtotime($join_date));
		$end_date = $this->input->get_post('end_date') != '' ? $this->input->get_post('end_date') : date('Y-m-d');
		
		$this->data['start_date'] = date('Y-m-d',strtotime($start_date));
		$this->data['end_date'] = date('Y-m-d',strtotime($end_date));
		
		// Set the validation rules
		$this->form_validation->set_rules('start_date', 'Start Date', 'required|trim|min_length[10]|max_length[10]');
		$this->form_validation->set_rules('end_date', 'End Date', 'required|trim|min_length[10]|max_length[10]');
		
		// If the validation worked
		if ($this->form_validation->run())
		{
			$report_data = $this->report_model->generate_report($restaurant_id,$start_date,$end_date);
			//my_var_dump($report_data);
			if($report_data === false)
			{
				$_SESSION['msg_error'][] = "No enough data for report generation.";
			}
			else
			{
				self::export_report_to_excel_file($report_data);
			}
		}
		
		$this->data['Active'] = 'Download';
		$this->load->view('download-report',$this->data);
	}
	
	public function auth()
	{
		$restaurant_id = $_SESSION[USER_LOGIN]['id'];
		$Password = $this->input->get_post('Password');
		if($_POST)
		{
			$sql = "SELECT * FROM restaurants WHERE id=$restaurant_id";
			if($_SESSION[USER_LOGIN]['report_password']=='')
			{
				$sql .= " AND password='".md5($Password)."'";
				$_SESSION[USER_RETURN_MSG]['Error']	= 1;
				$_SESSION[USER_RETURN_MSG]['Msg']	= 'Please create a separate report section password.';
			}
			else
				$sql .= " AND report_password='".md5($Password)."'";
			
			$query = $this->db->query($sql);
			if($query->num_rows())
			{
				$Error=0;
				$Msg='Loading Report. Please wait...';
				$ReturnPage = base_url().'report/';
			}
			else
			{
				$Error=1;
				$Msg='Wrong password. Please try again.';
				$ReturnPage = base_url().'report/auth';
			}
			$Return["Error"]= $Error;
			$Return["Msg"]	= $Msg;
			$Return['Url']	= $ReturnPage;
			echo json_encode($Return);
			exit;
		}
		$this->data['Active'] = 'report-auth';
		$this->load->view('report_auth',$this->data);
	}
	
	public function load_rank()
	{
		$restaurant_id = $_SESSION[USER_LOGIN]['id'];
		$business_type = $_SESSION[USER_LOGIN]['business_type'];
		
		$count_restaurant_by_business_type = $this->report_model->count_restaurant_by_business_type($business_type);
		
		$YourRank = $this->report_model->get_rank($restaurant_id,$business_type);
		
		echo "Your ranking among other $business_type competitors in Dubai is <strong>$YourRank</strong>/<span>$count_restaurant_by_business_type</span>";
	}
	
	public function top_low_graph()
	{
		$restaurant_id = $_SESSION[USER_LOGIN]['id'];
		$graph_interval = $this->input->get_post('graph_interval');
		$start_date = $this->input->get_post('start_date');
		$end_date = $this->input->get_post('end_date');
		
		$this->data['restaurant_id'] = $restaurant_id;
		$this->data['start_date'] = $start_date;
		$this->data['end_date'] = $end_date;
		$this->data['graph_interval'] = $graph_interval;
		
		# Get top and low rated item
		$top_low_item = $this->report_model->get_top_and_low_menu($restaurant_id,$start_date,$end_date);
		$top_item = $top_low_item[0];
		$low_item = $top_low_item[1];
		$this->data['top_item'] = $top_item;
		$this->data['low_item'] = $low_item;
		
		$this->data['heading'] = 'Report from '.date('d M Y',strtotime($start_date)).' to '.date('d M Y',strtotime($end_date));
		
		$graph_data = $this->report_model->get_top_low_graph_data($graph_interval,$start_date,$end_date,$top_item,$low_item);
		$this->data['graph_data'] = $graph_data;
		
		$this->load->view('report-toplow-chart',$this->data);
	}
	
	public function top_low_item()
	{
		$restaurant_id = $_SESSION[USER_LOGIN]['id'];
		$start_date = $this->input->get_post('start_date');
		$end_date = $this->input->get_post('end_date');
		
		# Get top and low rated item
		$top_low_item = $this->report_model->get_top_and_low_menu($restaurant_id,$start_date,$end_date);
		$return['top_item'] = $top_low_item[0];
		$return['low_item'] = $top_low_item[1];
		
		echo json_encode($return,JSON_NUMERIC_CHECK);
	}
	
	public function customer_experience_graph()
	{
		$restaurant_id = $_SESSION[USER_LOGIN]['id'];
		$graph_interval = $this->input->get_post('graph_interval');
		$start_date = $this->input->get_post('start_date');
		$end_date = $this->input->get_post('end_date');
		
		$this->data['restaurant_id'] = $restaurant_id;
		$this->data['start_date'] = $start_date;
		$this->data['end_date'] = $end_date;
		$this->data['graph_interval'] = $graph_interval;
		
		$this->data['heading'] = 'Report from '.date('d M Y',strtotime($start_date)).' to '.date('d M Y',strtotime($end_date));
		
		$graph_data = $this->report_model->get_customer_experience_graph_data($restaurant_id,$start_date,$end_date,$graph_interval);
		$this->data['graph_data'] = $graph_data;
		
		$this->load->view('report-customer-experience',$this->data);
	}
	
	public function order_speed_graph()
	{
		$restaurant_id = $_SESSION[USER_LOGIN]['id'];
		$graph_interval = $this->input->get_post('graph_interval');
		$start_date = $this->input->get_post('start_date');
		$end_date = $this->input->get_post('end_date');
		
		$this->data['restaurant_id'] = $restaurant_id;
		$this->data['start_date'] = $start_date;
		$this->data['end_date'] = $end_date;
		$this->data['graph_interval'] = $graph_interval;
		
		$graph_data = $this->report_model->get_order_speed_graph_data($restaurant_id,$start_date,$end_date);
		$this->data['graph_data'] = $graph_data;
		
		$this->load->view('report-order-speed',$this->data);
	}
	
	public function region_graph()
	{
		$restaurant_id = $_SESSION[USER_LOGIN]['id'];
		$graph_interval = $this->input->get_post('graph_interval');
		$start_date = $this->input->get_post('start_date');
		$end_date = $this->input->get_post('end_date');
		
		$this->data['restaurant_id'] = $restaurant_id;
		$this->data['start_date'] = $start_date;
		$this->data['end_date'] = $end_date;
		$this->data['graph_interval'] = $graph_interval;
		
		$graph_query = $this->report_model->get_region_graph_data($restaurant_id,$start_date,$end_date);
		$this->data['graph_query'] = $graph_query;
		
		$this->load->view('report-region',$this->data);
	}
	
	public function hearabout_graph()
	{
		$restaurant_id = $_SESSION[USER_LOGIN]['id'];
		$graph_interval = $this->input->get_post('graph_interval');
		$start_date = $this->input->get_post('start_date');
		$end_date = $this->input->get_post('end_date');
		
		$this->data['restaurant_id'] = $restaurant_id;
		$this->data['start_date'] = $start_date;
		$this->data['end_date'] = $end_date;
		$this->data['graph_interval'] = $graph_interval;
		
		$graph_query = $this->report_model->get_hearabout_graph_data($restaurant_id,$start_date,$end_date);
		$this->data['graph_query'] = $graph_query;
		
		$this->load->view('report-hearabout',$this->data);
	}
	
	public function comeagain_graph()
	{
		$restaurant_id = $_SESSION[USER_LOGIN]['id'];
		$graph_interval = $this->input->get_post('graph_interval');
		$start_date = $this->input->get_post('start_date');
		$end_date = $this->input->get_post('end_date');
		
		$this->data['restaurant_id'] = $restaurant_id;
		$this->data['start_date'] = $start_date;
		$this->data['end_date'] = $end_date;
		$this->data['graph_interval'] = $graph_interval;
		
		$graph_query = $this->report_model->get_comeagain_graph_data($restaurant_id,$start_date,$end_date);
		$this->data['graph_query'] = $graph_query;
		
		$this->load->view('report-comeagain',$this->data);
	}
	
	public function item_review()
	{
		$restaurant_id = $_SESSION[USER_LOGIN]['id'];
		$menu_id = $this->input->get_post('menu_id');
		$start_date = $this->input->get_post('start_date');
		$end_date = $this->input->get_post('end_date');
		
		$this->data['restaurant_id'] = $restaurant_id;
		$this->data['start_date'] = $start_date;
		$this->data['end_date'] = $end_date;
		$this->data['menu_id'] = $menu_id;
		
		$review_query = $this->report_model->get_item_ratings($menu_id,$start_date,$end_date);
		$this->data['review_query'] = $review_query;
		
		$this->load->view('item_review',$this->data);
	}
	
	public function item_graph()
	{
		$restaurant_id = $_SESSION[USER_LOGIN]['id'];
		$menu_id = $this->input->get_post('menu_id');
		$start_date = $this->input->get_post('start_date');
		$end_date = $this->input->get_post('end_date');
		
		$this->data['restaurant_id'] = $restaurant_id;
		$this->data['start_date'] = $start_date;
		$this->data['end_date'] = $end_date;
		$this->data['menu_id'] = $menu_id;
		
		$graph_data = $this->report_model->get_item_graph_data($menu_id,$start_date,$end_date);
		if(array_sum($graph_data))
		{
			$this->data['graph_data'] = $graph_data;
			//my_var_dump($graph_data);
			$this->load->view('item_graph',$this->data);
		}
		else
		{
			echo 'Data not available.';
		}
	}
	
	public function items_graph()
	{
		$restaurant_id = $_SESSION[USER_LOGIN]['id'];
		$graph_interval = $this->input->get_post('graph_interval');
		$start_date = $this->input->get_post('start_date');
		$end_date = $this->input->get_post('end_date');
		$items = $this->input->get_post('items');
		
		$this->data['restaurant_id'] = $restaurant_id;
		$this->data['start_date'] = $start_date;
		$this->data['end_date'] = $end_date;
		$this->data['graph_interval'] = $graph_interval;
		$this->data['items'] = $items;
		
		$this->data['heading'] = 'Report from '.date('d M Y',strtotime($start_date)).' to '.date('d M Y',strtotime($end_date));
		
		$graph_data = $this->report_model->get_items_graph_data($graph_interval,$start_date,$end_date,$items);
		$this->data['graph_data'] = $graph_data;
		
		$this->load->view('items_graph',$this->data);
	}
	
	public function export_email_addresses()
	{
		$restaurant_id = $_SESSION[USER_LOGIN]['id'];
		$start_date = $this->input->get_post('start_date');
		$end_date = $this->input->get_post('end_date');
		
		$this->db->select('email,date_created');
		$this->db->where('restaurant_id',$restaurant_id);
		$this->db->where('email !=','');
		$this->db->where('DATE(date_created) BETWEEN ',"DATE('$start_date') AND DATE('$end_date')",false);
		
		$this->db->order_by('date_created','DESC');
		
		$query = $this->db->get('ratings_view');
		
		$this->load->dbutil();
		
		$csv_data = $this->dbutil->csv_from_result($query);
		
		// Load the file helper and write the file to your server
		$this->load->helper('file');
		$filename = 'email-addresses-'.$restaurant_id.'.csv';
		$filepath = './'.UPLOADS.'/'.$filename;
		write_file($filepath, $csv_data); 
		
		// Load the download helper and send the file to your desktop
		$this->load->helper('download');
		$file_contents = file_get_contents($filepath);
		force_download($filename,$file_contents);
	}
	
	public function export_report_to_excel_file($data)
	{
		//activate worksheet number 1
		$this->excel->setActiveSheetIndex(0);
		//name the worksheet
		$this->excel->getActiveSheet()->setTitle("Pref.Menu Report");
		
		# Row 1
		$this->excel->getActiveSheet()->getStyle("C8:L40")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		
		
		$this->excel->getActiveSheet()->setCellValue("A1", $data["report_heading"]);
		//change the font size
		$this->excel->getActiveSheet()->getStyle("A1")->getFont()->setSize(20);
		//make the font become bold
		$this->excel->getActiveSheet()->getStyle("A1")->getFont()->setBold(true);
		//merge cell A1 until L1
		$this->excel->getActiveSheet()->mergeCells("A1:L1");
		//set aligment to center for that merged cell (A1 to D1)
		$this->excel->getActiveSheet()->getStyle("A1")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		
		$this->excel->getActiveSheet()->getStyle("A1")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("E8E5E5");
		
		$row = 3;
		$this->excel->getActiveSheet()->setCellValue("B$row", $data["total_reviews"][0]);
		$this->excel->getActiveSheet()->getStyle("B$row")->getFont()->setBold(true);
		$this->excel->getActiveSheet()->getStyle("B$row")->getFont()->setSize(16);
		$this->excel->getActiveSheet()->setCellValue("C$row", $data["total_reviews"][1]);
		$this->excel->getActiveSheet()->getStyle("C$row")->getFont()->setBold(true)->setSize(16);//->getColor()->setRGB("green");
		$this->excel->getActiveSheet()->getStyle("C$row")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		
		$row = 5;
		$this->excel->getActiveSheet()->mergeCells("C$row:E$row");
		$this->excel->getActiveSheet()->setCellValueByColumnAndRow(2, $row, 'Menu Item');
		$this->excel->getActiveSheet()->setCellValueByColumnAndRow(5, $row, 'Rating');
		$this->excel->getActiveSheet()->getStyle("C$row")->getFont()->setBold(true)->setSize(16);
		$this->excel->getActiveSheet()->getStyle("F$row")->getFont()->setBold(true)->setSize(16);
		
		$row = 6;
		$this->excel->getActiveSheet()->mergeCells("C$row:E$row");
		$this->excel->getActiveSheet()->setCellValueByColumnAndRow(1, $row, $data["top_item"][0]);
		$this->excel->getActiveSheet()->setCellValueByColumnAndRow(2, $row, $data["top_item"][1]);
		$this->excel->getActiveSheet()->setCellValueByColumnAndRow(5, $row, $data["top_item"][2]);
		$this->excel->getActiveSheet()->getStyle("B$row")->getFont()->setBold(true);
		$this->excel->getActiveSheet()->getStyle("C$row:F$row")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("FDE9D9");
		$this->excel->getActiveSheet()->getStyle("F$row")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		
		$row = 7;
		$this->excel->getActiveSheet()->mergeCells("C$row:E$row");
		$this->excel->getActiveSheet()->setCellValueByColumnAndRow(1, $row, $data["low_item"][0]);
		$this->excel->getActiveSheet()->setCellValueByColumnAndRow(2, $row, $data["low_item"][1]);
		$this->excel->getActiveSheet()->setCellValueByColumnAndRow(5, $row, $data["low_item"][2]);
		$this->excel->getActiveSheet()->getStyle("B$row")->getFont()->setBold(true);
		$this->excel->getActiveSheet()->getStyle("C$row:F$row")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("EAF1DD");
		$this->excel->getActiveSheet()->getStyle("F$row")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		
		$row = 9;
		$this->excel->getActiveSheet()->mergeCells("C$row:D$row")->getStyle("C$row")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("E5E0EC");
		$this->excel->getActiveSheet()->mergeCells("E$row:F$row")->getStyle("E$row")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("DDD9C3");
		$this->excel->getActiveSheet()->mergeCells("G$row:H$row")->getStyle("G$row")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("E5E0EC");
		$this->excel->getActiveSheet()->mergeCells("I$row:J$row")->getStyle("I$row")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("DDD9C3");
		$this->excel->getActiveSheet()->mergeCells("K$row:L$row")->getStyle("K$row")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("E5E0EC");
		$this->excel->getActiveSheet()->setCellValueByColumnAndRow(1, $row, $data["question_header"][0]);
		$this->excel->getActiveSheet()->setCellValueByColumnAndRow(2, $row, $data["question_header"][1]);
		$this->excel->getActiveSheet()->setCellValueByColumnAndRow(4, $row, $data["question_header"][2]);
		$this->excel->getActiveSheet()->setCellValueByColumnAndRow(6, $row, $data["question_header"][3]);
		$this->excel->getActiveSheet()->setCellValueByColumnAndRow(8, $row, $data["question_header"][4]);
		$this->excel->getActiveSheet()->setCellValueByColumnAndRow(10, $row, $data["question_header"][5]);
		$this->excel->getActiveSheet()->getStyle("B$row:L$row")->getFont()->setBold(true);
		$this->excel->getActiveSheet()->getStyle("B$row:L$row")->getFont()->setSize(16);
		
		$row = 10;
		$this->excel->getActiveSheet()->getStyle("B$row")->getFont()->setBold(true)->setSize(16);
		$this->excel->getActiveSheet()->setCellValueByColumnAndRow(1, $row, $data["cusotmer_experience_data"][0]);
		$this->excel->getActiveSheet()->setCellValueByColumnAndRow(2, $row, $data["cusotmer_experience_data"][1][0]);
		$this->excel->getActiveSheet()->setCellValueByColumnAndRow(3, $row, $data["cusotmer_experience_data"][1][1]);
		$this->excel->getActiveSheet()->setCellValueByColumnAndRow(4, $row, $data["cusotmer_experience_data"][2][0]);
		$this->excel->getActiveSheet()->setCellValueByColumnAndRow(5, $row, $data["cusotmer_experience_data"][2][1]);
		$this->excel->getActiveSheet()->setCellValueByColumnAndRow(6, $row, $data["cusotmer_experience_data"][3][0]);
		$this->excel->getActiveSheet()->setCellValueByColumnAndRow(7, $row, $data["cusotmer_experience_data"][3][1]);
		$this->excel->getActiveSheet()->setCellValueByColumnAndRow(8, $row, $data["cusotmer_experience_data"][4][0]);
		$this->excel->getActiveSheet()->setCellValueByColumnAndRow(9, $row, $data["cusotmer_experience_data"][4][1]);
		$this->excel->getActiveSheet()->setCellValueByColumnAndRow(10, $row, $data["cusotmer_experience_data"][5][0]);
		$this->excel->getActiveSheet()->setCellValueByColumnAndRow(11, $row, $data["cusotmer_experience_data"][5][1]);
		$this->excel->getActiveSheet()->getStyle("C$row")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("FDE9D9");
		$this->excel->getActiveSheet()->getStyle("D$row")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("EAF1DD");
		$this->excel->getActiveSheet()->getStyle("E$row")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("FDE9D9");
		$this->excel->getActiveSheet()->getStyle("F$row")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("EAF1DD");
		$this->excel->getActiveSheet()->getStyle("G$row")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("FDE9D9");
		$this->excel->getActiveSheet()->getStyle("H$row")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("EAF1DD");
		$this->excel->getActiveSheet()->getStyle("I$row")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("FDE9D9");
		$this->excel->getActiveSheet()->getStyle("J$row")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("EAF1DD");
		$this->excel->getActiveSheet()->getStyle("K$row")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("FDE9D9");
		$this->excel->getActiveSheet()->getStyle("L$row")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("EAF1DD");
		
		$row = 12;
		$this->excel->getActiveSheet()->getStyle("B$row")->getFont()->setBold(true)->setSize(16);
		$this->excel->getActiveSheet()->setCellValueByColumnAndRow(1, $row, $data["order_speed_data"][0]);
		$this->excel->getActiveSheet()->setCellValueByColumnAndRow(2, $row, $data["order_speed_data"][1][0]);
		$this->excel->getActiveSheet()->setCellValueByColumnAndRow(3, $row, $data["order_speed_data"][1][1]);
		$this->excel->getActiveSheet()->setCellValueByColumnAndRow(4, $row, $data["order_speed_data"][2][0]);
		$this->excel->getActiveSheet()->setCellValueByColumnAndRow(5, $row, $data["order_speed_data"][2][1]);
		$this->excel->getActiveSheet()->setCellValueByColumnAndRow(6, $row, $data["order_speed_data"][3][0]);
		$this->excel->getActiveSheet()->setCellValueByColumnAndRow(7, $row, $data["order_speed_data"][3][1]);
		$this->excel->getActiveSheet()->setCellValueByColumnAndRow(8, $row, $data["order_speed_data"][4][0]);
		$this->excel->getActiveSheet()->setCellValueByColumnAndRow(9, $row, $data["order_speed_data"][4][1]);
		$this->excel->getActiveSheet()->setCellValueByColumnAndRow(10, $row, $data["order_speed_data"][5][0]);
		$this->excel->getActiveSheet()->setCellValueByColumnAndRow(11, $row, $data["order_speed_data"][5][1]);
		$this->excel->getActiveSheet()->getStyle("C$row")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("FDE9D9");
		$this->excel->getActiveSheet()->getStyle("D$row")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("EAF1DD");
		$this->excel->getActiveSheet()->getStyle("E$row")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("FDE9D9");
		$this->excel->getActiveSheet()->getStyle("F$row")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("EAF1DD");
		$this->excel->getActiveSheet()->getStyle("G$row")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("FDE9D9");
		$this->excel->getActiveSheet()->getStyle("H$row")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("EAF1DD");
		$this->excel->getActiveSheet()->getStyle("I$row")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("FDE9D9");
		$this->excel->getActiveSheet()->getStyle("J$row")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("EAF1DD");
		$this->excel->getActiveSheet()->getStyle("K$row")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("FDE9D9");
		$this->excel->getActiveSheet()->getStyle("L$row")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("EAF1DD");
		
		$row = 14;
		$this->excel->getActiveSheet()->mergeCells("C$row:D$row")->getStyle("C$row")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("E5E0EC");
		$this->excel->getActiveSheet()->mergeCells("E$row:F$row")->getStyle("E$row")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("DDD9C3");
		$this->excel->getActiveSheet()->mergeCells("G$row:H$row")->getStyle("G$row")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("E5E0EC");
		$this->excel->getActiveSheet()->setCellValueByColumnAndRow(1, $row, $data["come_again_data"][0]);
		$this->excel->getActiveSheet()->setCellValueByColumnAndRow(2, $row, "Yes");
		$this->excel->getActiveSheet()->setCellValueByColumnAndRow(4, $row, "No");
		$this->excel->getActiveSheet()->setCellValueByColumnAndRow(6, $row, "May be");
		$this->excel->getActiveSheet()->getStyle("B$row:H$row")->getFont()->setBold(true);
		$this->excel->getActiveSheet()->getStyle("B$row:H$row")->getFont()->setSize(16);
		
		$row = 15;
		$this->excel->getActiveSheet()->setCellValueByColumnAndRow(2, $row, $data["come_again_data"][1][0]);
		$this->excel->getActiveSheet()->setCellValueByColumnAndRow(3, $row, $data["come_again_data"][1][1]);
		$this->excel->getActiveSheet()->setCellValueByColumnAndRow(4, $row, $data["come_again_data"][2][0]);
		$this->excel->getActiveSheet()->setCellValueByColumnAndRow(5, $row, $data["come_again_data"][2][1]);
		$this->excel->getActiveSheet()->setCellValueByColumnAndRow(6, $row, $data["come_again_data"][3][0]);
		$this->excel->getActiveSheet()->setCellValueByColumnAndRow(7, $row, $data["come_again_data"][3][1]);
		$this->excel->getActiveSheet()->getStyle("C$row")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("FDE9D9");
		$this->excel->getActiveSheet()->getStyle("D$row")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("EAF1DD");
		$this->excel->getActiveSheet()->getStyle("E$row")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("FDE9D9");
		$this->excel->getActiveSheet()->getStyle("F$row")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("EAF1DD");
		$this->excel->getActiveSheet()->getStyle("G$row")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("FDE9D9");
		$this->excel->getActiveSheet()->getStyle("H$row")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("EAF1DD");
		
		$row = 17;
		$this->excel->getActiveSheet()->setCellValueByColumnAndRow(1, $row, $data["staff_data_heading"][0]);
		$this->excel->getActiveSheet()->setCellValueByColumnAndRow(2, $row, $data["staff_data_heading"][1]);
		$this->excel->getActiveSheet()->setCellValueByColumnAndRow(3, $row, $data["staff_data_heading"][2]);
		$this->excel->getActiveSheet()->getStyle("B$row")->getFont()->setBold(true);
		$this->excel->getActiveSheet()->getStyle("B$row")->getFont()->setSize(16);
		$this->excel->getActiveSheet()->getStyle("C$row")->getFont()->setBold(true);
		$this->excel->getActiveSheet()->getStyle("C$row")->getFont()->setSize(16);
		$this->excel->getActiveSheet()->getStyle("D$row")->getFont()->setBold(true);
		$this->excel->getActiveSheet()->getStyle("D$row")->getFont()->setSize(16);
		
		require_once APPPATH."/third_party/PHPExcel/Worksheet/Drawing.php";
		$row = 18;
		foreach($data["staff_data"] as $staff_data)
		{
			$this->excel->getActiveSheet()->setCellValueByColumnAndRow(1, $row, $staff_data[0]);
			$this->excel->getActiveSheet()->setCellValueByColumnAndRow(3, $row, $staff_data[1]);
			$this->excel->getActiveSheet()->setCellValueByColumnAndRow(4, $row, $staff_data[2]);
			$this->excel->getActiveSheet()->getStyle("D$row")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("FDE9D9");
			$this->excel->getActiveSheet()->getStyle("E$row")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("EAF1DD");
			
			$row_color = @$row_color == 'E5E0EC' ? 'DDD9C3' : 'E5E0EC';
			$this->excel->getActiveSheet()->getStyle("B$row:C$row")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB($row_color);
			
			$staff_data[3] = $staff_data[3] == '' ? 'employee.png' : $staff_data[3];
			
			$objDrawing = new PHPExcel_Worksheet_Drawing();
			$objDrawing->setName($staff_data[3]);
			$objDrawing->setDescription($staff_data[3]);
			$objDrawing->setWorksheet($this->excel->getActiveSheet());
			$objDrawing->setPath('./uploads/'.$staff_data[3]);
			//$objDrawing->setWidth(50);
			//$objDrawing->setHeight(50);
			$objDrawing->setWidthAndHeight(50,50);
			$objDrawing->setResizeProportional(true);
			$objDrawing->setCoordinates('C'.$row);
			$objDrawing->setOffsetX(50 - $objDrawing->getWidth() );
			$objDrawing->setOffsetY(50 - $objDrawing->getHeight() + 8 );
			
			$this->excel->getActiveSheet()->getRowDimension($row)->setRowHeight(50);
			$this->excel->getActiveSheet()->getStyle("A$row:E$row")->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
			
			$row++;
		}
		
		$this->excel->getActiveSheet()->setShowGridlines(false);
		$this->excel->getActiveSheet()->getColumnDimension("B")->setWidth(40);
		
		$filename="pref_menu_report.xls"; //save our workbook as this file name
		header('Content-Type: application/vnd.ms-excel'); //mime type
		header('Content-Disposition: attachment;filename="'.$filename.'"'); //tell browser what's the file name
		header('Cache-Control: max-age=0'); //no cache
					
		//save it to Excel5 format (excel 2003 .XLS file), change this to 'Excel2007' (and adjust the filename extension, also the header mime type)
		//if you want to save it as .XLSX Excel 2007 format
		$objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');  
		//force user to download the Excel file without writing it to server's HD
		$objWriter->save('php://output');
	}
}











