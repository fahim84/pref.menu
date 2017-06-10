<?php

defined('BASEPATH') OR exit('No direct script access allowed');

// This can be removed if you use __autoload() in config.php OR use Modular Extensions
require APPPATH . '/libraries/REST_Controller.php';


class Menu extends REST_Controller {

    function __construct()
    {
        // Construct the parent class
        parent::__construct();
        $this->load->model('restaurant_model');
        $this->load->model('login_model');
        $this->load->model('category_model');
    }

    public function get_get($restaurant_id)
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

        $this->data['status']= TRUE;
        $this->data['categories'] = $this->category_model->get_list($where_condition, $order_by);
        $this->set_response($this->data, REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
    }

    public function add_post($restaurant_id){
        $restaurant = $this->login_model->get_user_by_id($restaurant_id);
        if(!$restaurant){
            // Set the response and exit
            $this->response([
                'status' => FALSE,
                'message' => 'Restaurant does not exist.'
            ], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
        }
        $newCategory = $this->input->get_post('new_category');

        if($newCategory != NULL && $newCategory != '') {
            $categoryData['restaurant_id'] = $restaurant_id;
            $categoryData['title'] = $this->input->get_post('new_category');
            $category_id = $this->restaurant_model->add_category($categoryData);
        }else
        {
            $category_id = $this->input->get_post('category_id');
        }

        $insert_array['restaurant_id'] = $restaurant_id;
        $insert_array['category_id'] = $category_id;
        $insert_array['title'] = $this->input->get_post('title');
        $insert_array['price'] = $this->input->get_post('price')?$this->input->get_post('price'):0;
        $insert_array['menu_number'] = $this->input->get_post('menu_number')==''?10000:$this->input->get_post('menu_number');
        $insert_array['description'] = $this->input->get_post('description');
        $insert_array['popular'] = $this->input->get_post('popular')!='' ? $this->input->get_post('popular') : 0;

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
            $insert_array['image'] = $image;

            # Get width and height of uploaded file
            $image_path = './'.UPLOADS.'/'.$image;
            $width = get_width($image_path);
            $height = get_height($image_path);

            # Resize Image Now
            $width > RESIZE_IF_PIXELS_LIMIT ? resize_image2($image_path, RESIZE_IMAGE_WIDTH, '', 'W') : '';
            $height > RESIZE_IF_PIXELS_LIMIT ? resize_image2($image_path, '', RESIZE_IMAGE_HEIGHT, 'H') : '';
        }else
        {
            $uploaded_file_array = (isset($_FILES['image']) and $_FILES['image']['size'] > 0 and $_FILES['image']['error'] == 0) ? $_FILES['image'] : '';
            # Show uploading error only when the file uploading attempt exist.
            if( is_array($uploaded_file_array) ) {
                // Set the response and exit
                $this->response([
                    'status' => FALSE,
                    'message' => $this->upload->display_errors()
                ], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
            }
        }

        $this->restaurant_model->add_menu($insert_array);

        $this->data['status'] = True;
        $this->data['message'] = 'Item added successfully!';
        $this->set_response($this->data, REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
    }

    public function update_post($id){
        $menu = $this->restaurant_model->get_menu_by_id($id);
        if(!$menu) {
            $this->response([
                'status' => FALSE,
                'message' => 'Menu not found.'
            ], REST_Controller::HTTP_FORBIDDEN); // HTTP_FORBIDDEN (403) being the HTTP response code
        }

        $newCategory = $this->input->get_post('new_category');

        if($newCategory != NULL && $newCategory != '') {
            $categoryData['restaurant_id'] = $menu->restaurant_id;
            $categoryData['title'] = $this->input->get_post('new_category');
            $category_id = $this->restaurant_model->add_category($categoryData);
        }else
        {
            $category_id = $this->input->get_post('category_id');
        }

        $delete_image = $this->input->post('delete_image');

        $update_array['category_id'] = $category_id;
        $update_array['title'] = $this->input->get_post('title');
        $update_array['price'] = $this->input->get_post('price')?$this->input->get_post('price'):0;
        $update_array['menu_number'] = $this->input->get_post('menu_number')==''?10000:$this->input->get_post('menu_number');
        $update_array['description'] = $this->input->get_post('description');
        $update_array['popular'] = $this->input->get_post('popular')!='' ? $this->input->get_post('popular') : 0;

        # File uploading configuration
        $config['upload_path'] = './'.UPLOADS.'/';
        $config['allowed_types'] = 'gif|jpg|png|jpeg';
        $config['encrypt_name'] = true;
        $this->load->library('upload', $config);

        if ($this->upload->do_upload('image'))
        {
            $upload_detail = $this->upload->data();
            $image = $upload_detail['file_name'];
            $update_array['image'] = $image;

            # Get width and height of uploaded file
            $image_path = './'.UPLOADS.'/'.$image;
            $width = get_width($image_path);
            $height = get_height($image_path);

            # Resize Image Now
            $width > RESIZE_IF_PIXELS_LIMIT ? resize_image2($image_path, RESIZE_IMAGE_WIDTH, '', 'W') : '';
            $height > RESIZE_IF_PIXELS_LIMIT ? resize_image2($image_path, '', RESIZE_IMAGE_HEIGHT, 'H') : '';

            $previous_image = $menu->image;
            delete_file('./'.UPLOADS.'/'.$previous_image);

        }
        elseif ($delete_image != null && $delete_image == 1){
            $previous_image = $menu->image;
            $update_array['image'] = '';
            delete_file('./'.UPLOADS.'/'.$previous_image);
        }

        # Update data into database
        $this->restaurant_model->update_menu($id,$update_array);

        $this->data['status'] = True;
        $this->data['message'] = 'Menu updated successfully!';
        $this->data['menu'] =  $this->restaurant_model->get_menu_by_id($id);
        $this->set_response($this->data, REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
    }

    public function delete_delete($id){
        $menu = $this->restaurant_model->get_menu_by_id($id);
        if(!$menu) {
            $this->response([
                'status' => FALSE,
                'message' => 'Menu not found.'
            ], REST_Controller::HTTP_FORBIDDEN); // HTTP_FORBIDDEN (403) being the HTTP response code
        }

        $this->restaurant_model->delete_menu($id);
        $this->data['status'] = True;
        $this->data['message'] = 'Menu deleted successfully';
        $this->set_response($this->data, REST_Controller::HTTP_OK); // OK (200) being the HTTP response code

    }
}
