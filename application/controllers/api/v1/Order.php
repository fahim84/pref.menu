<?php

defined('BASEPATH') OR exit('No direct script access allowed');

// This can be removed if you use __autoload() in config.php OR use Modular Extensions
require APPPATH . '/libraries/REST_Controller.php';


class Order extends REST_Controller {

    function __construct()
    {
        // Construct the parent class
        parent::__construct();
        $this->load->model('order_model');
        $this->load->model('login_model');
        $this->load->model('restaurant_model');
    }

    public function submit_post()
    {
        $input_data = json_decode(trim(file_get_contents('php://input')), true);
        if(!isset($input_data['restaurant_id']) || !isset($input_data['table_number']) || !isset($input_data['menus']) || !isset($input_data['temporary'])){
            // Set the response and exit
            $this->response([
                'status' => FALSE,
                'message' => 'Wrong Parameters'
            ], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
        }

        $restaurant = $this->login_model->get_user_by_id($input_data['restaurant_id']);
        if(!$restaurant){
            // Set the response and exit
            $this->response([
                'status' => FALSE,
                'message' => 'Restaurant does not exist.'
            ], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
        }

        $insert_array = array();
        $insert_array['restaurant_id'] = $input_data['restaurant_id'];
        $insert_array['table_number'] = $input_data['table_number'];
        $insert_array['customer_number'] = 1;
        $insert_array['temporary'] = $input_data['temporary'];
        $insert_array['order_timestamp'] = time();

        if($this->order_model->check_existing_order_on_same_table($input_data['restaurant_id'],$input_data['table_number'],$input_data['temporary'],time()))
        {
            $where_condition['DATE(date_created)'] = array("'".date('Y-m-d')."'",FALSE); // If you set it to FALSE, CodeIgniter will not try to protect your field or table names.
            $where_condition['order_timestamp !='] = time();
            $where_condition['review_done'] = 0;
            $where_condition['restaurant_id'] = $input_data['restaurant_id'];
            $where_condition['table_number'] = $input_data['table_number'];
            $where_condition['deleted'] = 0;
            $where_condition['temporary'] = $input_data['temporary'];
            // delete existing orders of same table
            $this->order_model->update_orders($where_condition, array('deleted' => 1) );
        }

        $order_id = $this->order_model->add_order($insert_array);

        if(!$order_id){
            $this->response([
                'status' => FALSE,
                'message' => 'Order could not be saved. Try again'
            ], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code

        }
        $menus = $input_data['menus'];

        foreach($menus as $menu)
        {
            $insert_array = array();
            $insert_array['order_id'] = $order_id;
            $insert_array['menu_id'] = $menu['id'];
            $insert_array['quantity'] = $menu['quantity'];
            $insert_array['request_comment'] = $menu['request_comment'];
            $this->order_model->add_order_item($insert_array);
        }


        $staffQuery = $this->restaurant_model->get_staffs($input_data['restaurant_id']);

        $this->data['order_id'] = $order_id;
        $this->data['session_id'] = session_id();
        $this->data['staff'] = $staffQuery->result_array();
        $this->set_response($this->data, REST_Controller::HTTP_OK); // OK (200) being the HTTP response code

    }

    public function rating_post(){
        $input_data = json_decode(trim(file_get_contents('php://input')), true);
        if(!isset($input_data['restaurant_id']) || !isset($input_data['order_id']) || !isset($input_data['table_number']) || !isset($input_data['session_id']) || !isset($input_data['order_ratings'])){
            // Set the response and exit
            $this->response([
                'status' => FALSE,
                'message' => 'Wrong Parameters'
            ], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
        }
        $negative_feedback = false;

        //$where_condition['DATE(date_created)'] = array("'".date('Y-m-d')."'",FALSE); // If you set it to FALSE, CodeIgniter will not try to protect your field or table names.
        $where_condition['id'] = $input_data['order_id'];
        $where_condition['review_done'] = 1;
        $where_condition['restaurant_id'] = $input_data['restaurant_id'];
        $where_condition['table_number'] = $input_data['table_number'];
        $where_condition['deleted'] = 0;
        $order_by['customer_number'] = 'ASC';
        $orders_query = $this->order_model->get_orders($where_condition, $order_by);
        $total_orders = $orders_query->num_rows();

        if($total_orders)
        {
            // Set the response and exit
            $this->response([
                'status' => FALSE,
                'message' => 'Already submitted review of this order.'
            ], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
        }

        $this->order_model->update_order($input_data['order_id'], array('review_done' => 1,'order_done' => 1) );

        $insert_data = array();
        $insert_data['restaurant_id'] = $input_data['restaurant_id'];
        $insert_data['order_id'] = $input_data['order_id'];
        $insert_data['table_number'] = $input_data['table_number'];
        $insert_data['name'] = $input_data['name'];
        $insert_data['email'] = $input_data['email'];
        $insert_data['region'] = ucfirst($input_data['region']);
        $insert_data['hear_about_us'] = ucfirst($input_data['hear_about_us']);
        $insert_data['customer_experience'] = $input_data['customer_experience'];
        $insert_data['order_speed'] = $input_data['order_speed'];
        $insert_data['come_again'] = $input_data['come_again'];
        $insert_data['staff_id'] = $input_data['staff_id'];
        $insert_data['suggestion'] = ucfirst($input_data['suggestion']);

        $rating_id = $this->order_model->add_rating($insert_data);
        $menu_ratings = $input_data['order_ratings'];

        foreach($menu_ratings as $menu_rating)
        {
            $insert_data = array();
            $insert_data['rating_id'] = $rating_id;
            $insert_data['menu_id'] = $menu_rating['menu_id'];
            $insert_data['rate'] = $menu_rating['rate'];
            $insert_data['item_comment'] = $menu_rating['item_comment'];

            if($menu_rating['rate'] < 3){
                $negative_feedback = true;
            }

            $this->order_model->add_rating_item($insert_data);
        }


        if($insert_data['come_again'] == 'No' || $insert_data['come_again'] < 3 || $insert_data['customer_experience'] < 3) // 2 means NO, Customer will not come again
        {
            $negative_feedback = true;
        }

        if($negative_feedback) // if Negative Feedback
        {
            $user = $this->login_model->get_user_by_id($input_data['restaurant_id']);
            $to = $user['email'];
            $Subject = 'Negative Feedback Alert';
            ob_start();

            $this->data['rating_id'] = $rating_id;
            $this->load->view('email-feedback',$this->data);

            $message = ob_get_contents();
            ob_end_clean();

            $this->load->library('email');

            # Send email to Signup User
            $this->email->clear(TRUE);
            $this->email->set_mailtype("html");
            $this->email->from(SYSTEM_EMAIL, SYSTEM_NAME);
            $this->email->to($to);
            $this->email->subject($Subject);
            $this->email->message(get_email_message_with_wrapper($message));
            $this->email->send();
        }
        $this->data['status'] = TRUE;
        $this->data['message'] = 'Thank you for your feedback and we hope to see you again soon.';
        $this->set_response($this->data,REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
        
    }

    public function existing_get($restaurant_id){
        $restaurant = $this->login_model->get_user_by_id($restaurant_id);
        if(!$restaurant){
            // Set the response and exit
            $this->response([
                'status' => FALSE,
                'message' => 'Restaurant does not exist.'
            ], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
        }
        $where_condition['DATE(date_created) >='] = array("'".date('Y-m-d')."'",FALSE); // If you set it to FALSE, CodeIgniter will not try to protect your field or table names.
        $where_condition['review_done'] = 0;
        $where_condition['restaurant_id'] = $restaurant_id;
        $where_condition['deleted'] = 0;
        $where_condition['temporary'] = 0;
        $order_by['id'] = 'ASC';
        $orders = $this->order_model->get_orders($where_condition, $order_by);
        $result = array();
        foreach ($orders->result_array() as $order){
            $resultArray = $order;
            $resultArray['item'] = $this->order_model->get_order_items($order['id'])->result_array();
            array_push($result,$resultArray);
        }
        $this->data['orders'] = $result;
        $this->set_response($this->data, REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
    }

    public function table_get($restaurant_id, $table_no){
        $restaurant = $this->login_model->get_user_by_id($restaurant_id);
        if(!$restaurant){
            // Set the response and exit
            $this->response([
                'status' => FALSE,
                'message' => 'Restaurant does not exist.'
            ], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
        }
        $where_condition['DATE(date_created)'] = array("'".date('Y-m-d')."'",FALSE); // If you set it to FALSE, CodeIgniter will not try to protect your field or table names.
        $where_condition['review_done'] = 0;
        $where_condition['restaurant_id'] = $restaurant_id;
        $where_condition['table_number'] = $table_no;
        $where_condition['deleted'] = 0;
        $where_condition['temporary'] = 0;
        $order_by['customer_number'] = 'ASC';

        $orders = $this->order_model->get_orders($where_condition, $order_by);

        $total_orders = $orders->num_rows();
        if($total_orders == 0)
        {
            // Set the response and exit
            $this->response([
                'status' => FALSE,
                'message' => 'No recent order found for this table'
            ], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
        }else {
            $result = array();
            foreach ($orders->result_array() as $order){
                $resultArray = $order;
                $resultArray['item'] = $this->order_model->get_order_items($order['id'])->result_array();
                array_push($result,$resultArray);
            }
            $this->data['status'] = TRUE;
            $this->data['orders'] = $result;
        }
        $this->set_response($this->data, REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
    }

    public function edit_post($order_id){
        $input_data = json_decode(trim(file_get_contents('php://input')), true);

        if(!isset($input_data['table_number']) || !isset($input_data['menus'])){
            // Set the response and exit
            $this->response([
                'status' => FALSE,
                'message' => 'Wrong Parameters'
            ], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
        }

        $order = $this->order_model->get_order_by_id($order_id);

        if(!$order){
            // Set the response and exit
            $this->response([
                'status' => FALSE,
                'message' => 'Order does not exist.'
            ], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
        }

        $this->order_model->update_order($order_id, array('table_number' => $input_data['table_number']));

        # Delete previous all records for this order
        $this->order_model->delete_all_items_of_this_order($order_id);

        $menus = $input_data['menus'];

        foreach($menus as $menu)
        {
            $insert_array = array();
            $insert_array['order_id'] = $order_id;
            $insert_array['menu_id'] = $menu['id'];
            $insert_array['quantity'] = $menu['quantity'];
            $insert_array['request_comment'] = $menu['request_comment'];
            $this->order_model->add_order_item($insert_array);
        }

        $this->data['status'] = TRUE;
        $this->data['message'] = 'Order updated successfully!';
        $this->set_response($this->data, REST_Controller::HTTP_OK); // OK (200) being the HTTP response code

    }

    public function delete_post()
    {
        $input_data = json_decode(trim(file_get_contents('php://input')), true);
        if(!isset($input_data['order_ids'])){
            // Set the response and exit
            $this->response([
                'status' => FALSE,
                'message' => 'Wrong Parameters'
            ], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
        }
        $selected_ids = $input_data['order_ids'];
        $explodedIds = explode(',', $selected_ids);
        $deleteTotal = 0;
        if( is_array($explodedIds) and count($explodedIds) )
        {
            foreach($explodedIds as $order_id)
            {
                $where_condition['id'] = $order_id;
                $where_condition['deleted'] = 1;
                $orders_query = $this->order_model->get_orders($where_condition);
                $deletedOrder = $orders_query->num_rows();
                if(!$deletedOrder) {
                    $order = $this->order_model->update_order($order_id, array('deleted' => 1) );
                    if($order) {
                        $deleteTotal++;
                    }else {
                        // Set the response and exit
                        $this->response([
                            'status' => FALSE,
                            'message' => 'Could not delete Orders. Something went wrong.'
                        ], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
                    }
                }
            }
            $this->data['status'] = TRUE;
            $this->data['message'] = $deleteTotal." Record deleted...";
            $this->set_response($this->data, REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
        }
        else
        {
            // Set the response and exit
            $this->response([
                'status' => FALSE,
                'message' => 'Could not delete Orders. Something went wrong.'
            ], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
        }
    }
}
