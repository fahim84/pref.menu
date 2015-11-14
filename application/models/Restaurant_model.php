<?php
class Restaurant_model extends CI_Model
{
	function __construct()
	{
		parent::__construct();
	}
	
	public function delete_category($id)
	{
		$menus = self::get_menus_by_category($id);
			
		foreach($menus->result() as $row)
		{
			$menu_id = $row->id;
			self::delete_menu($menu_id);
		}
		
		return $this->db->delete('categories', array('id' => $id));
	}
	
	public function delete_menu($id)
	{
		$query = $this->db->get_where('menus', array('id' => $id));
		foreach($query->result() as $row)
		{
			if(delete_file('./'.UPLOADS.'/'.$row->image))
			{
				$_SESSION['msg_success'][] = "$row->image file deleted...";
			}
		}
		return $this->db->delete('menus', array('id' => $id));
	}
	
	public function delete_staff($id)
	{
		$query = $this->db->get_where('staffs', array('id' => $id));
		foreach($query->result() as $row)
		{
			if(delete_file('./'.UPLOADS.'/'.$row->image))
			{
				$_SESSION['msg_success'][] = "$row->image file deleted...";
			}
		}
		return $this->db->delete('staffs', array('id' => $id));
	}
	
	public function add_category($data)
	{
		if($this->db->insert('categories', $data))
		{
			return $this->db->insert_id();
		}
		return false;
	}
	public function add_menu($data)
	{
		$data['date_created'] = date('Y-m-d H:i:s');
		$data['date_updated'] = date('Y-m-d H:i:s');
		
		if($this->db->insert('menus', $data))
		{
			return $this->db->insert_id();
		}
		return false;
	}
	
	public function add_staff($data)
	{
		$data['date_created'] = date('Y-m-d H:i:s');
		$data['date_updated'] = date('Y-m-d H:i:s');
		
		if($this->db->insert('staffs', $data))
		{
			return $this->db->insert_id();
		}
		return false;
	}
	
	public function update_menu($id,$data)
	{
		$data['date_updated'] = date('Y-m-d H:i:s');
		$this->db->where('id', $id);
		return $this->db->update('menus',$data);
	}
	
	public function update_staff($id,$data)
	{
		$data['date_updated'] = date('Y-m-d H:i:s');
		$this->db->where('id', $id);
		return $this->db->update('staffs',$data);
	}
	
	public function get_menu_by_id($id)
	{
		$query = $this->db->get_where('menus',array('id'=>$id));
		return $query->num_rows() ? $query->row() : false;
	}
	
	public function get_staff_by_id($id)
	{
		$query = $this->db->get_where('staffs',array('id'=>$id));
		return $query->num_rows() ? $query->row() : false;
	}
	
	public function get_categories($restaurant_id)
	{
		return $this->db->get_where('categories',array('restaurant_id'=>$restaurant_id));
	}
	
	public function get_menus($where=array(), $order_by=array('id'=>'ASC') )
	{
		$this->db->select('menus.*, categories.title as category');
		foreach($order_by as $column => $direction)
		{
			$this->db->order_by($column,$direction);
		}
		foreach($where as $column => $value)
		{
			!is_array($value) ? $this->db->where($column,$value) : $this->db->where($column,$value[0],$value[1]);
		}
		
		$this->db->where('menus.category_id','categories.id',FALSE);
		$query = $this->db->get('menus,categories');
		//my_var_dump($this->db->last_query());
		return $query;
	}
	
	public function get_staffs($restaurant_id)
	{
		return $this->db->query("SELECT * FROM staffs WHERE restaurant_id=$restaurant_id ORDER BY id ASC");
	}
	
	public function get_menus_by_category($category_id)
	{
		return $this->db->query("SELECT *,(SELECT title FROM categories WHERE id=menus.category_id) category FROM menus WHERE category_id=$category_id ORDER BY menu_number ASC");
	}
	
	public function check_menu_staff($restaurant_id)
	{
		$this->db->where('restaurant_id',$restaurant_id);
		$this->db->from('menus');
		$count_menus = $this->db->count_all_results();
		
		$this->db->where('restaurant_id',$restaurant_id);
		$this->db->from('staffs');
		$count_staffs = $this->db->count_all_results();
		
		$Msg='';
		if($count_menus==0 && $count_staffs>0)
			$Msg = 'Please complete the &lsquo;Menu&rsquo; page from the drop down list located top right of your screen';
		else if($count_menus>0 && $count_staffs==0)
			$Msg = 'Please complete the &lsquo;Staff&rsquo; page from the drop down list located top right of your screen';
		else if($count_menus==0 && $count_staffs==0)
			$Msg = 'Please complete the &lsquo;Menu&rsquo; &amp; &lsquo;Staff&rsquo; pages from the drop down list located top right of your screen';
		
		
		return $Msg;
	}
}


