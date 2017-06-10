<?php

defined('BASEPATH') OR exit('No direct script access allowed');

// This can be removed if you use __autoload() in config.php OR use Modular Extensions
require APPPATH . '/libraries/REST_Controller.php';


class Restaurant extends REST_Controller {

    function __construct()
    {
        // Construct the parent class
        parent::__construct();
        $this->load->model('login_model');
        $this->load->model('restaurant_model');

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

        $restaurant = $this->login_model->get_user_by_id($restaurant_id);
        $this->data['status'] = TRUE;
        $this->data['user'] = $restaurant;
        $this->set_response($this->data, REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
    }

    public function update_post($restaurant_id){
        $restaurant = $this->login_model->get_user_by_id($restaurant_id);
        if(!$restaurant){
            // Set the response and exit
            $this->response([
                'status' => FALSE,
                'message' => 'Restaurant does not exist.'
            ], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
        }
        $delete_logo = $this->input->post('delete_logo');

        $new_data['business_type'] = $this->input->post('business_type');
        $new_data['ordering_feature'] = $this->input->post('ordering_feature');
        $new_data['name'] = $this->input->post('name');
        $new_data['manager_name'] = $this->input->post('manager_name');
        $new_data['address'] = $this->input->post('address');
        $new_data['phone'] = $this->input->post('phone');

        $password = $this->input->post('password');

        # File uploading configuration
        $config['upload_path'] = './'.UPLOADS.'/';
        $config['allowed_types'] = 'gif|jpg|png|jpeg';
        $config['encrypt_name'] = true;
        $this->load->library('upload', $config);

        if ($this->upload->do_upload('logo'))
        {
            $upload_detail = $this->upload->data();
            $logo = $upload_detail['file_name'];
            $new_data['logo'] = $logo;

            # Get width and height of uploaded file
            $image_path = './'.UPLOADS.'/'.$logo;
            $width = get_width($image_path);
            $height = get_height($image_path);

            # Resize Image Now
            $width > RESIZE_IF_PIXELS_LIMIT ? resize_image2($image_path, RESIZE_IMAGE_WIDTH, '', 'W') : '';
            $height > RESIZE_IF_PIXELS_LIMIT ? resize_image2($image_path, '', RESIZE_IMAGE_HEIGHT, 'H') : '';

            $previous_image = $restaurant['logo'];
            delete_file('./'.UPLOADS.'/'.$previous_image);

        }
        elseif ($delete_logo != null && $delete_logo == 1){
            $previous_image = $restaurant['logo'];
            $new_data['logo'] = '';
            delete_file('./'.UPLOADS.'/'.$previous_image);
        }else {
            $uploaded_file_array = (isset($_FILES['image']) and $_FILES['image']['size'] > 0 and $_FILES['image']['error'] == 0) ? $_FILES['image'] : '';
            # Show uploading error only when the file uploading attempt exist.
            if( is_array($uploaded_file_array) ) {
                $this->response([
                    'status' => FALSE,
                    'message' => $this->upload->display_errors()
                ], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
            }
        }

        if($password!='')
        {
            $new_data['password'] = md5($password);
        }

        # Update data into database
        $this->login_model->update_user($restaurant_id,$new_data);

        $this->data['status'] = True;
        $this->data['message'] = 'Profile Updated Successfully.';
        $this->set_response($this->data, REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
    }

    public function check_data_get($id){
        $Msg = html_entity_decode($this->restaurant_model->check_menu_staff($id));
        if($Msg != '')
        {
            $this->response([
                'status' => FALSE,
                'message' => $Msg
            ], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
        } else {
            $this->data['status'] = True;
            $this->data['message'] = 'Restaurant contains both staff and menus';
        }
        $this->set_response($this->data, REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
    }
}
