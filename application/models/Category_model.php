<?php
class Category_model extends CI_Model
{
	function __construct()
	{
		parent::__construct();
	}

    public function get_category_by_id($id)
    {
        $query = $this->db->get_where('categories',array('id'=>$id));
        return $query->num_rows() ? $query->row() : false;
    }

	public function get_list($where=array(), $order_by=array('id'=>'ASC') )
	{
		foreach($order_by as $column => $direction)
		{
			$this->db->order_by($column,$direction);
		}
		foreach($where as $column => $value)
		{
			!is_array($value) ? $this->db->where($column,$value) : $this->db->where($column,$value[0],$value[1]);
		}

		$query = $this->db->get('categories');
		$result = array();
		foreach ($query->result_array() as $row)
		{
			$resultArray = $row;
			$query = $this->db->where('category_id',$row['id'])->order_by('menu_number','ASC')->get('menus');
			$resultArray['menus'] = $query->result_array();
			array_push($result,$resultArray);
		}
		return $result;
	}
}


