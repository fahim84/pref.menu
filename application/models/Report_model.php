<?php
class Report_model extends CI_Model
{
	function __construct()
	{
		parent::__construct();
	}
	
	public function count_feedbacks($restaurant_id,$start_date,$end_date)
	{
		$this->db->where("restaurant_id",$restaurant_id);
		$this->db->where('DATE(date_created) BETWEEN ',"DATE('$start_date') AND DATE('$end_date')",false);
		$this->db->from('ratings');
		$count_feedbacks = $this->db->count_all_results();
		//my_var_dump($this->db->last_query());
		
		return $count_feedbacks;
	}
	
	public function get_top_staff_member($restaurant_id,$start_date,$end_date)
	{
		$count_feedbacks = self::count_feedbacks($restaurant_id,$start_date,$end_date);
		
		if($count_feedbacks)
		{
			$sql = "SELECT COUNT(*) number_of_rating, COUNT(*) / $count_feedbacks * 100 percent, ratings_view.*
				FROM ratings_view
				WHERE (staff_id IN(SELECT id FROM staffs WHERE restaurant_id=$restaurant_id) OR staff_id=0) 
				AND DATE(date_created) BETWEEN DATE('$start_date') AND DATE('$end_date')
				GROUP BY staff_id 
				ORDER BY COUNT(*) DESC LIMIT 1";
			$query = $this->db->query($sql);
			return $query->num_rows() ? $query->row() : false;
		}
		return false;
	}
	
	public function count_restaurant_by_business_type($business_type)
	{
		$this->db->where('business_type' , $business_type);
		$this->db->from('restaurants');
		return $this->db->count_all_results();
	}
	
	public function get_rank($restaurant_id,$business_type)
	{
		$query = $this->db->query("SELECT *,(SELECT SUM(customer_experience+order_speed) FROM ratings WHERE restaurant_id=restaurants.id) total_rating FROM 
		restaurants WHERE business_type='$business_type' ORDER BY total_rating DESC");
		$i = 1;
		foreach($query->result() as $row)
		{
			if($row->id == $restaurant_id)
			{
				return $i;
			}
			$i++;
		}
		return $i;
	}
	
	public function get_top_and_low_menu($restaurant_id,$start_date,$end_date)
	{
		$sql = "SELECT *, AVG(rate) average_rate FROM ratings_items_view 
		WHERE restaurant_id=$restaurant_id 
		AND DATE(date_created) BETWEEN '$start_date' AND '$end_date'
		GROUP BY menu_id
		ORDER BY average_rate DESC LIMIT 1";
		$query = $this->db->query($sql);
		$top_item = $query->row();
		$top_item->title2 = truncate_string($top_item->title,13);
		$top_item->average_rate = number_format($top_item->average_rate,2);
		
		$sql = "SELECT *, AVG(rate) average_rate FROM ratings_items_view 
		WHERE restaurant_id=$restaurant_id 
		AND DATE(date_created) BETWEEN '$start_date' AND '$end_date'
		GROUP BY menu_id
		ORDER BY average_rate ASC LIMIT 1";
		$query = $this->db->query($sql); 
		$low_item = $query->row();
		$low_item->title2 = truncate_string($low_item->title,13);
		$low_item->average_rate = number_format($low_item->average_rate,2);
		
		return array($top_item,$low_item);
	}
	
	public function get_top_low_graph_data($graph_interval,$start_date,$end_date,$top_item,$low_item)
	{
		if($graph_interval == 'Daily')
		{
			$graph_data[] = array('Date', 'Top Rated '.$top_item->title, 'Low Rated '.$low_item->title);
			
			# get all the dates of rating.
			$query = $this->db->query("SELECT DATE(date_created) date_created FROM ratings_items_view 
			WHERE menu_id IN ($top_item->menu_id, $low_item->menu_id) 
			AND DATE(date_created) BETWEEN '$start_date' AND '$end_date'
			GROUP BY DATE(date_created)");
			
			foreach($query->result() as $row)
			{
				$date_created = $row->date_created;
				
				$query_top = $this->db->query("SELECT AVG(rate) average_rating FROM ratings_items WHERE DATE(date_created) = '$date_created' AND menu_id=$top_item->menu_id");
				$top_row = $query_top->row();
				$top_average_rating = $top_row->average_rating > 0 ? number_format($top_row->average_rating,2) : NULL;
				
				
				$query_low = $this->db->query("SELECT AVG(rate) average_rating FROM ratings_items WHERE DATE(date_created) = '$date_created' AND menu_id=$low_item->menu_id");
				$low_row = $query_low->row();
				$low_average_rating = $low_row->average_rating > 0 ? number_format($low_row->average_rating,2) : NULL;
				
				$graph_data[] = array($date_created, $top_average_rating, $low_average_rating);
			}
			
			if( ! isset($date_created) ) return false;
			
			return $graph_data;
		}
		
		if($graph_interval == 'Monthly')
		{
			$graph_data[] = array('Date', 'Top Rated '.$top_item->title, 'Low Rated '.$low_item->title);
			
			# get all the dates of rating.
			$query = $this->db->query("SELECT DATE_FORMAT(date_created, '%Y-%m') mdate FROM ratings_items_view 
			WHERE menu_id IN ($top_item->menu_id, $low_item->menu_id) 
			AND DATE(date_created) BETWEEN '$start_date' AND '$end_date'
			GROUP BY DATE_FORMAT(date_created, '%Y-%m')");
			
			foreach($query->result() as $row)
			{
				$mdate = $row->mdate;
				
				$query_top = $this->db->query("SELECT AVG(rate) average_rating FROM ratings_items WHERE DATE_FORMAT(date_created, '%Y-%m') = '$mdate' AND menu_id=$top_item->menu_id");
				$top_row = $query_top->row();
				$top_average_rating = $top_row->average_rating > 0 ? number_format($top_row->average_rating,2) : NULL;
				
				$query_low = $this->db->query("SELECT AVG(rate) average_rating FROM ratings_items WHERE DATE_FORMAT(date_created, '%Y-%m') = '$mdate' AND menu_id=$low_item->menu_id");
				$low_row = $query_low->row();
				$low_average_rating = $low_row->average_rating > 0 ? number_format($low_row->average_rating,2) : NULL;
				
				$graph_data[] = array($mdate, $top_average_rating, $low_average_rating);
			}
			
			if( ! isset($mdate) ) return false;
			
			return $graph_data;
		}
	}
	
	public function get_customer_experience_graph_data($restaurant_id,$start_date,$end_date,$graph_interval)
	{
		if($graph_interval == 'Daily')
		{
			$graph_data[] = array('Date', 'Average Rating');
	
			$query = $this->db->query("SELECT AVG(customer_experience) average_rating,DATE(`date_created`) date_created
					FROM ratings_view 
					WHERE restaurant_id=$restaurant_id
					AND DATE(`date_created`) BETWEEN '$start_date' AND '$end_date'
					GROUP BY DATE(`date_created`)");
			foreach($query->result() as $row)
			{
				$date_created = $row->date_created;
				$average_rating = number_format($row->average_rating,2);
				
				$graph_data[] = array($date_created, $average_rating);
			}
			
			if( ! isset($date_created) ) return false;
			
			return $graph_data;
		}
		
		if($graph_interval == 'Monthly')
		{
			$start    = new DateTime($start_date);
			$start->modify('first day of this month');
			$end      = new DateTime($end_date);
			$end->modify('first day of next month');
			$interval = DateInterval::createFromDateString('1 month');
			$period   = new DatePeriod($start, $interval, $end);
			
			$graph_data[] = array('Month', 'Average Rating');
			foreach ($period as $dt) 
			{
				$cDate = $dt->format("Y-m");
				
				
				$query = $this->db->query("SELECT AVG(customer_experience) average_rating,DATE(`date_created`) date_created
						FROM ratings_view 
						WHERE restaurant_id=$restaurant_id
						AND DATE_FORMAT(`date_created`, '%Y-%m')='$cDate'");
				
				foreach($query->result() as $row)
				{
					$date_created = $row->date_created;
					$average_rating = number_format($row->average_rating,2);
					
					$graph_data[] = array($date_created, $average_rating);
				}
			}
			
			if( ! isset($cDate) ) return false;
			
			return $graph_data;
		}
	}
	
	public function get_order_speed_graph_data($restaurant_id,$start_date,$end_date)
	{
		$query = $this->db->query("SELECT COUNT(*) count_rows FROM ratings WHERE restaurant_id=$restaurant_id AND order_speed=1 AND DATE(date_created) BETWEEN '$start_date' AND '$end_date'");
		$count1 = $query->row()->count_rows;
		$query = $this->db->query("SELECT COUNT(*) count_rows FROM ratings WHERE restaurant_id=$restaurant_id AND order_speed=2 AND DATE(date_created) BETWEEN '$start_date' AND '$end_date'");
		$count2 = $query->row()->count_rows;
		$query = $this->db->query("SELECT COUNT(*) count_rows FROM ratings WHERE restaurant_id=$restaurant_id AND order_speed=3 AND DATE(date_created) BETWEEN '$start_date' AND '$end_date'");
		$count3 = $query->row()->count_rows;
		$query = $this->db->query("SELECT COUNT(*) count_rows FROM ratings WHERE restaurant_id=$restaurant_id AND order_speed=4 AND DATE(date_created) BETWEEN '$start_date' AND '$end_date'");
		$count4 = $query->row()->count_rows;
		$query = $this->db->query("SELECT COUNT(*) count_rows FROM ratings WHERE restaurant_id=$restaurant_id AND order_speed=5 AND DATE(date_created) BETWEEN '$start_date' AND '$end_date'");
		$count5 = $query->row()->count_rows;
		
		$total_count = $count1 + $count2 + $count3 + $count4 + $count5;
		
		$percent1 = ($count1 / $total_count) * 100;
		$percent2 = ($count2 / $total_count) * 100;
		$percent3 = ($count3 / $total_count) * 100;
		$percent4 = ($count4 / $total_count) * 100;
		$percent5 = ($count5 / $total_count) * 100;
		
		$return[1] = number_format($percent1, 0);
        $return[2] = number_format($percent2, 0);
        $return[3] = number_format($percent3, 0);
        $return[4] = number_format($percent4, 0);
        $return[5] = number_format($percent5, 0);
		
		return $return;
	}
	
	public function get_region_graph_data($restaurant_id,$start_date,$end_date)
	{
		$query = $this->db->query("SELECT COUNT(*) number_of_customers,region, 
			COUNT(*)*100/(SELECT COUNT(*) FROM ratings WHERE restaurant_id=$restaurant_id) percentage
			FROM ratings WHERE restaurant_id=$restaurant_id 
			AND DATE(`date_created`) BETWEEN '$start_date' AND '$end_date'
			GROUP BY region ORDER BY number_of_customers DESC");
		return $query;
	}
	
	public function get_hearabout_graph_data($restaurant_id,$start_date,$end_date)
	{
		$query = $this->db->query("SELECT COUNT(*) number_of_customers,hear_about_us, 
			COUNT(*)*100/(SELECT COUNT(*) FROM ratings WHERE restaurant_id=$restaurant_id) percentage
			FROM ratings WHERE restaurant_id=$restaurant_id 
			AND DATE(`date_created`) BETWEEN '$start_date' AND '$end_date'
			GROUP BY hear_about_us ORDER BY number_of_customers DESC");
		return $query;
	}
	
	public function get_comeagain_graph_data($restaurant_id,$start_date,$end_date)
	{
		$query = $this->db->query("SELECT COUNT(*) number_of_customers,come_again, 
			COUNT(*)*100/(SELECT COUNT(*) FROM ratings WHERE restaurant_id=$restaurant_id) percentage
			FROM ratings WHERE restaurant_id=$restaurant_id 
			AND DATE(`date_created`) BETWEEN '$start_date' AND '$end_date'
			GROUP BY come_again ORDER BY number_of_customers DESC");
		return $query;
	}
	
	public function get_item_ratings($menu_id,$start_date,$end_date)
	{
		$this->db->where("DATE(date_created) BETWEEN ","'$start_date' AND '$end_date'", false);
		return $this->db->get_where('ratings_items_view',array('menu_id'=>$menu_id));
	}
	
	public function get_item_graph_data($menu_id,$start_date,$end_date)
	{
		$query = $this->db->query("SELECT COUNT(*) number_of_customers,rate, 
		COUNT(*)*100/(SELECT COUNT(*) FROM ratings_items_view WHERE menu_id=$menu_id AND DATE(`date_created`) BETWEEN '$start_date' AND '$end_date') percentage
		FROM ratings_items_view WHERE menu_id=$menu_id 
		AND DATE(`date_created`) BETWEEN '$start_date' AND '$end_date'
		GROUP BY rate ORDER BY rate ASC");
		
		$return[1] = 0;
        $return[2] = 0;
        $return[3] = 0;
        $return[4] = 0;
        $return[5] = 0;
		
		foreach($query->result() as $row)
		{
			$return[$row->rate] = number_format($row->percentage,0);
		}
		
		return $return;
	}
	
	public function get_items_graph_data($graph_interval,$start_date,$end_date,$items)
	{
		$comma_saparated_items = implode(',',$items);
		
		# Exclude items which are not rated yet
		
		if($graph_interval == 'Daily')
		{
			# get all the dates of rating.
			$query = $this->db->query("SELECT DATE(date_created) date_created FROM ratings_items_view 
			WHERE menu_id IN ($comma_saparated_items) 
			AND DATE(date_created) BETWEEN '$start_date' AND '$end_date'
			GROUP BY DATE(date_created)");
			
			foreach($query->result() as $row)
			{
				$date_created[] = $row->date_created;
			}
			
			if( ! isset($date_created) ) return false;
			
			# Loop through all items
			$query = $this->db->query("SELECT * FROM menus WHERE id IN ( SELECT menu_id FROM ratings_items_view WHERE menu_id IN ($comma_saparated_items) AND DATE(date_created) BETWEEN '$start_date' AND '$end_date' GROUP BY menu_id )");
			$total_items = $query->num_rows();
			$legends[] = 'Month';
			foreach($query->result() as $row)
			{
				$item_id = $row->id;
				$legends[] = $row->title;
				
				foreach ($date_created as $dt) // loop through each date
				{
					$date_for_graph_display = $dt;
					
					$query2 = $this->db->query("SELECT AVG(rate) average_rate FROM ratings_items_view WHERE menu_id=$item_id AND DATE(date_created)='$dt'");
					
					$average_rate = $query2->row()->average_rate;
					$average_rate = $average_rate ? number_format($average_rate,2) : NULL ;
					$item_rates[$date_for_graph_display][$item_id] = $average_rate;
				}
			}
			
			$graph_data[] = $legends;
			
			foreach($item_rates as $month => $rate)
			{
				$nextrow[0] = $month;
				foreach($rate as $rt)
				{
					$nextrow[] = $rt;
				}
				$graph_data[] = $nextrow;
				unset($nextrow);
			}
			
			return $graph_data;
		}
		
		if($graph_interval == 'Monthly')
		{
			# get all the dates of rating.
			$query = $this->db->query("SELECT DATE_FORMAT(date_created, '%Y-%m') mdate FROM ratings_items_view 
			WHERE menu_id IN ($comma_saparated_items) 
			AND DATE(date_created) BETWEEN '$start_date' AND '$end_date'
			GROUP BY DATE_FORMAT(date_created, '%Y-%m')");
			
			foreach($query->result() as $row)
			{
				$date_created[] = $row->mdate;
			}
			
			if( ! isset($date_created) ) return false;
			
			# Loop through all items
			$query = $this->db->query("SELECT * FROM menus WHERE id IN ( SELECT menu_id FROM ratings_items_view WHERE menu_id IN ($comma_saparated_items) AND DATE(date_created) BETWEEN '$start_date' AND '$end_date' GROUP BY menu_id )");
			$total_items = $query->num_rows();
			$legends[] = 'Month';
			foreach($query->result() as $row)
			{
				$item_id = $row->id;
				$legends[] = $row->title;
				
				foreach ($date_created as $dt) // loop through each date
				{
					$date_for_graph_display = $dt;
					
					$query2 = $this->db->query("SELECT AVG(rate) average_rate FROM ratings_items_view WHERE menu_id=$item_id AND DATE_FORMAT(date_created, '%Y-%m')='$dt'");
					
					$average_rate = $query2->row()->average_rate;
					$average_rate = $average_rate ? number_format($average_rate,2) : NULL ;
					$item_rates[$date_for_graph_display][$item_id] = $average_rate;
				}
			}
			
			$graph_data[] = $legends;
			
			foreach($item_rates as $month => $rate)
			{
				$nextrow[0] = $month;
				foreach($rate as $rt)
				{
					$nextrow[] = $rt;
				}
				$graph_data[] = $nextrow;
				unset($nextrow);
			}
			
			return $graph_data;
		}
	}
	
	public function get_ratings($where=array(), $order_by=array('id'=>'ASC'), $count_result = false )
	{
		foreach($order_by as $column => $direction)
		{
			$this->db->order_by($column,$direction);
		}
		foreach($where as $column => $value)
		{
			!is_array($value) ? $this->db->where($column,$value) : $this->db->where($column,$value[0],$value[1]);
		}
		
		# If true, count results and return it
		if($count_result)
		{
			$this->db->from('ratings_view');
			return $this->db->count_all_results();
		}
		
		$query = $this->db->get('ratings_view');
		//my_var_dump($this->db->last_query());
		return $query;
	}
	
	public function get_rating_items($rating_id)
	{
		return $this->db->get_where('ratings_items_view',array('rating_id'=>$rating_id));
	}
	
	public function get_customer_experience_data($restaurant_id,$start_date,$end_date)
	{
		$query = $this->db->query("SELECT COUNT(*) count_rows FROM ratings WHERE restaurant_id=$restaurant_id AND customer_experience=1 AND DATE(date_created) BETWEEN '$start_date' AND '$end_date'");
		$count1 = $query->row()->count_rows;
		$query = $this->db->query("SELECT COUNT(*) count_rows FROM ratings WHERE restaurant_id=$restaurant_id AND customer_experience=2 AND DATE(date_created) BETWEEN '$start_date' AND '$end_date'");
		$count2 = $query->row()->count_rows;
		$query = $this->db->query("SELECT COUNT(*) count_rows FROM ratings WHERE restaurant_id=$restaurant_id AND customer_experience=3 AND DATE(date_created) BETWEEN '$start_date' AND '$end_date'");
		$count3 = $query->row()->count_rows;
		$query = $this->db->query("SELECT COUNT(*) count_rows FROM ratings WHERE restaurant_id=$restaurant_id AND customer_experience=4 AND DATE(date_created) BETWEEN '$start_date' AND '$end_date'");
		$count4 = $query->row()->count_rows;
		$query = $this->db->query("SELECT COUNT(*) count_rows FROM ratings WHERE restaurant_id=$restaurant_id AND customer_experience=5 AND DATE(date_created) BETWEEN '$start_date' AND '$end_date'");
		$count5 = $query->row()->count_rows;
		
		$total_count = $count1 + $count2 + $count3 + $count4 + $count5;
		
		$percent1 = ($count1 / $total_count) * 100;
		$percent2 = ($count2 / $total_count) * 100;
		$percent3 = ($count3 / $total_count) * 100;
		$percent4 = ($count4 / $total_count) * 100;
		$percent5 = ($count5 / $total_count) * 100;
		
		$return['count1'] = $count1;
		$return['count2'] = $count2;
		$return['count3'] = $count3;
		$return['count4'] = $count4;
		$return['count5'] = $count5;
		$return['percent1'] = number_format($percent1, 0);
        $return['percent2'] = number_format($percent2, 0);
        $return['percent3'] = number_format($percent3, 0);
        $return['percent4'] = number_format($percent4, 0);
        $return['percent5'] = number_format($percent5, 0);
		$return['total_count'] = $total_count;
		
		return $return;
	}
	
	public function get_order_speed_data($restaurant_id,$start_date,$end_date)
	{
		$query = $this->db->query("SELECT COUNT(*) count_rows FROM ratings WHERE restaurant_id=$restaurant_id AND order_speed=1 AND DATE(date_created) BETWEEN '$start_date' AND '$end_date'");
		$count1 = $query->row()->count_rows;
		$query = $this->db->query("SELECT COUNT(*) count_rows FROM ratings WHERE restaurant_id=$restaurant_id AND order_speed=2 AND DATE(date_created) BETWEEN '$start_date' AND '$end_date'");
		$count2 = $query->row()->count_rows;
		$query = $this->db->query("SELECT COUNT(*) count_rows FROM ratings WHERE restaurant_id=$restaurant_id AND order_speed=3 AND DATE(date_created) BETWEEN '$start_date' AND '$end_date'");
		$count3 = $query->row()->count_rows;
		$query = $this->db->query("SELECT COUNT(*) count_rows FROM ratings WHERE restaurant_id=$restaurant_id AND order_speed=4 AND DATE(date_created) BETWEEN '$start_date' AND '$end_date'");
		$count4 = $query->row()->count_rows;
		$query = $this->db->query("SELECT COUNT(*) count_rows FROM ratings WHERE restaurant_id=$restaurant_id AND order_speed=5 AND DATE(date_created) BETWEEN '$start_date' AND '$end_date'");
		$count5 = $query->row()->count_rows;
		
		$total_count = $count1 + $count2 + $count3 + $count4 + $count5;
		
		$percent1 = ($count1 / $total_count) * 100;
		$percent2 = ($count2 / $total_count) * 100;
		$percent3 = ($count3 / $total_count) * 100;
		$percent4 = ($count4 / $total_count) * 100;
		$percent5 = ($count5 / $total_count) * 100;
		
		$return['count1'] = $count1;
		$return['count2'] = $count2;
		$return['count3'] = $count3;
		$return['count4'] = $count4;
		$return['count5'] = $count5;
		$return['percent1'] = number_format($percent1, 0);
        $return['percent2'] = number_format($percent2, 0);
        $return['percent3'] = number_format($percent3, 0);
        $return['percent4'] = number_format($percent4, 0);
        $return['percent5'] = number_format($percent5, 0);
		$return['total_count'] = $total_count;
		
		return $return;
	}
	
	public function generate_report($restaurant_id,$start_date,$end_date)
	{
		# Get rating
		$where_condition['restaurant_id'] = $restaurant_id;
		$where_condition['DATE(date_created) BETWEEN '] = array("DATE('$start_date') AND DATE('$end_date')",FALSE); // If you set it to FALSE, CodeIgniter will not try to protect your field or table names.
		$order_by['id'] = 'DESC';
		$total_number_of_ratings = self::get_ratings($where_condition, $order_by, true);
		//my_var_dump($total_number_of_ratings);
		if($total_number_of_ratings < 2)
		{
			return false;
		}
        //my_var_dump(array("Report Summary From ".$start_date." to ".$end_date));
		//my_var_dump(array("Total Reviews", $total_number_of_ratings));
		$return_data['report_heading'] = "Report Summary From ".$start_date." to ".$end_date;
		$return_data['total_reviews'] = array("Total Reviews", $total_number_of_ratings);
		
		# Get top and low rated item
		$top_low_item = self::get_top_and_low_menu($restaurant_id,$start_date,$end_date);
		$top_item = $top_low_item[0];
		$low_item = $top_low_item[1];
		
		$return_data['top_item'] = array("Highest Rated Menu Item", $top_item->title, $top_item->average_rate);
		$return_data['low_item'] = array("Lowest Rated Menu Item", $low_item->title, $low_item->average_rate);
		
		$return_data['question_header'] = array('', "1 Star", "2 Star", "3 Star", "4 Star", "5 Star");
		
		# Get Customer Experience Data
		$customer_experience_data = self::get_customer_experience_data($restaurant_id,$start_date,$end_date);
		$customer_experience_data1 = array($customer_experience_data['count1'], $customer_experience_data['percent1'].'%');
		$customer_experience_data2 = array($customer_experience_data['count2'], $customer_experience_data['percent2'].'%');
		$customer_experience_data3 = array($customer_experience_data['count3'], $customer_experience_data['percent3'].'%');
		$customer_experience_data4 = array($customer_experience_data['count4'], $customer_experience_data['percent4'].'%');
		$customer_experience_data5 = array($customer_experience_data['count5'], $customer_experience_data['percent5'].'%');
		
		$return_data['cusotmer_experience_data'] = array("Customer Experience", $customer_experience_data1, $customer_experience_data2, $customer_experience_data3, $customer_experience_data4, $customer_experience_data5);
		
		# Get Order Speed Data
		$order_speed_data = self::get_order_speed_data($restaurant_id,$start_date,$end_date);
		$order_speed_data1 = array($order_speed_data['count1'], $order_speed_data['percent1'].'%');
		$order_speed_data2 = array($order_speed_data['count2'], $order_speed_data['percent2'].'%');
		$order_speed_data3 = array($order_speed_data['count3'], $order_speed_data['percent3'].'%');
		$order_speed_data4 = array($order_speed_data['count4'], $order_speed_data['percent4'].'%');
		$order_speed_data5 = array($order_speed_data['count5'], $order_speed_data['percent5'].'%');
		
		$return_data['order_speed_data'] = array("Speed of Service", $order_speed_data1, $order_speed_data2, $order_speed_data3, $order_speed_data4, $order_speed_data5);
		
		# Get Come Again Data
		$come_again_query = self::get_comeagain_graph_data($restaurant_id,$start_date,$end_date);
		
		$Yes = array(0,0);
		$No = array(0,0);
		$Maybe = array(0,0);
		
		foreach($come_again_query->result() as $row)
		{
			$percent = number_format($row->percentage,0);
			
			if($row->come_again == 'Yes')
			{
				$Yes = array($row->number_of_customers,$percent);
			}
			elseif($row->come_again == 'No')
			{
				$No = array($row->number_of_customers,$percent);
			}
			else
			{
				$Maybe = array($row->number_of_customers,$percent);
			}
		}
		
		$Yes = array($Yes[0], $Yes[1].'%');
		$No = array($No[0], $No[1].'%');
		$Maybe = array($Maybe[0], $Maybe[1].'%');
		
		$return_data['come_again_data'] = array('Would customers come back?',$Yes, $No, $Maybe);
		
		
		# Get Staff Members data
		$return_data['staff_data_heading'] = array("Most Popular Employee",'','');
		
		$sql = "SELECT COUNT(*) number_of_rating, COUNT(*) / $total_number_of_ratings * 100 percent, ratings_view.*
				FROM ratings_view
				WHERE (staff_id IN(SELECT id FROM staffs WHERE restaurant_id=$restaurant_id) OR staff_id=0) 
				AND DATE(date_created) BETWEEN DATE('$start_date') AND DATE('$end_date')
				GROUP BY staff_id 
				ORDER BY COUNT(*) DESC";
		$query = $this->db->query($sql);
		
		foreach($query->result() as $row)
		{
			$percent = number_format($row->percent,0).'%';
			$staff_data[] = array($row->staff_name.' '.$row->designation, $row->number_of_rating, $percent, $row->image);
		}
		
		$return_data['staff_data'] = $staff_data;
		
		return $return_data;
	}
}


