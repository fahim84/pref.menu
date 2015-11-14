<?php
class Order_model extends CI_Model
{
	function __construct()
	{
		parent::__construct();
	}
	
	
	public function add_order($data)
	{
		$data['date_created'] = date('Y-m-d H:i:s');
		$data['date_updated'] = date('Y-m-d H:i:s');
		$data['session_id'] = session_id();
		
		if($this->db->insert('orders', $data))
		{
			return $this->db->insert_id();
		}
		return false;
	}
	
	public function add_order_item($data)
	{
		if($this->db->insert('orders_items', $data))
		{
			return $this->db->insert_id();
		}
		return false;
	}
	
	public function update_order($id,$data)
	{
		$data['date_updated'] = date('Y-m-d H:i:s');
		$this->db->where('id', $id);
		return $this->db->update('orders',$data);
	}
	
	public function get_order_by_id($id)
	{
		$query = $this->db->get_where('orders',array('id'=>$id));
		return $query->num_rows() ? $query->row() : false;
	}
	
	public function get_orders($where=array(), $order_by=array('id'=>'ASC') )
	{
		foreach($order_by as $column => $direction)
		{
			$this->db->order_by($column,$direction);
		}
		foreach($where as $column => $value)
		{
			!is_array($value) ? $this->db->where($column,$value) : $this->db->where($column,$value[0],$value[1]);
		}
		
		$query = $this->db->get('orders');
		//my_var_dump($this->db->last_query());
		return $query;
	}
	
	public function get_order_items($order_id)
	{
		return $this->db->get_where('orders_items_view',array('order_id'=>$order_id));
	}
	
	public function check_existing_order_on_same_table_and_customer($restaurant_id,$table_number,$customer_number,$temporary)
	{
		$query = $this->db->query("SELECT * FROM `orders` WHERE DATE(date_created)>='".date('Y-m-d')."' AND customer_number=$customer_number AND `table_number`=$table_number AND review_done=0 AND restaurant_id=$restaurant_id AND `deleted`=0 AND `temporary`=$temporary");
		return $query->num_rows() ? $query->row() : false;
	}
	
	public function check_existing_order_on_same_table($restaurant_id,$table_number,$temporary,$order_timestamp)
	{
		$query = $this->db->query("SELECT * FROM `orders` WHERE DATE(date_created)>='".date('Y-m-d')."' AND `table_number`=$table_number AND review_done=0 AND restaurant_id=$restaurant_id AND `deleted`=0 AND `temporary`=$temporary AND order_timestamp!=$order_timestamp");
		return $query->num_rows() ? true : false;
	}
	
	public function count_temporary_pending_orders($restaurant_id,$table_number)
	{
		$this->db->where('session_id',session_id());
		$this->db->where('restaurant_id',$restaurant_id);
		$this->db->where('table_number',$table_number);
		$this->db->where('review_done',0);
		$this->db->where('deleted',0);
		$this->db->where('temporary',1);
		$this->db->where('DATE(date_created)',"'".date('Y-m-d')."'",false);
		$this->db->from('orders');
		$count_temporary_pending_orders = $this->db->count_all_results();
		//my_var_dump($this->db->last_query());
		
		return $count_temporary_pending_orders;
	}
	
	public function add_rating($data)
	{
		$data['date_created'] = date('Y-m-d H:i:s');
		$data['date_updated'] = date('Y-m-d H:i:s');
		
		if($this->db->insert('ratings', $data))
		{
			return $this->db->insert_id();
		}
		return false;
	}
	
	public function add_rating_item($data)
	{
		$data['date_created'] = date('Y-m-d H:i:s');
		$data['date_updated'] = date('Y-m-d H:i:s');
		
		if($this->db->insert('ratings_items', $data))
		{
			return $this->db->insert_id();
		}
		return false;
	}
	
	public function delete_all_items_of_this_order($order_id)
	{
		return $this->db->delete('orders_items', array('order_id' => $order_id));
	}
	
	public function delete_all_temporary_pending_orders($restaurant_id)
	{
		$this->db->where('session_id',session_id());
		$this->db->where('restaurant_id',$restaurant_id);
		$this->db->where('review_done',0);
		$this->db->where('temporary',1);
		$this->db->where('DATE(date_created)',"'".date('Y-m-d')."'",false);
		$this->db->from('orders');
		
		$this->db->delete('orders');
		//my_var_dump($this->db->last_query());
	}
	
	public function update_orders($where=array(),$data)
	{
		$data['date_updated'] = date('Y-m-d H:i:s');
		
		foreach($where as $column => $value)
		{
			!is_array($value) ? $this->db->where($column,$value) : $this->db->where($column,$value[0],$value[1]);
		}
		
		return $this->db->update('orders',$data);
	}
	
	public function delete_order($id)
	{
		$this->db->where('id',$id);
		$this->db->delete('orders');
	}
}


