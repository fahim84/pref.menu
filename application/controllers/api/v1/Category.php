<?php

defined('BASEPATH') OR exit('No direct script access allowed');

// This can be removed if you use __autoload() in config.php OR use Modular Extensions
require APPPATH . '/libraries/REST_Controller.php';


class Category extends REST_Controller {

    function __construct()
    {
        // Construct the parent class
        parent::__construct();
        $this->load->model('category_model');
        $this->load->model('restaurant_model');
        $this->load->model('login_model');
    }

    public function list_get($restaurant_id)
    {
        $restaurant = $this->login_model->get_user_by_id($restaurant_id);
        if(!$restaurant){
            // Set the response and exit
            $this->response([
                'status' => FALSE,
                'message' => 'Restaurant does not exist.'
            ], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
        }

        $where_condition['categories.restaurant_id'] = $restaurant_id;
        $order_by['id'] = 'ASC';

        $this->data['categories'] = $this->category_model->get_list($where_condition, $order_by);
        $this->set_response($this->data, REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
    }

    public function delete_delete($category_id){

        $category = $this->category_model->get_category_by_id($category_id);
        if(!$category){
            // Set the response and exit
            $this->response([
                'status' => FALSE,
                'message' => 'Category does not exist.'
            ], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
        }

        $this->restaurant_model->delete_category($category_id);

        $this->data['status'] = True;
        $this->data['message'] = 'Category deleted successfully';
        $this->set_response($this->data, REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
    }
}
