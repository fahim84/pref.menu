<?php

defined('BASEPATH') OR exit('No direct script access allowed');

// This can be removed if you use __autoload() in config.php OR use Modular Extensions
require APPPATH . '/libraries/REST_Controller.php';


class Staff extends REST_Controller {

    function __construct()
    {
        // Construct the parent class
        parent::__construct();
        $this->load->model('restaurant_model');
        $this->load->model('login_model');

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

        $this->data['status']= TRUE;
        $this->data['staff'] = $this->restaurant_model->get_staffs($restaurant_id)->result_array();
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
        $insert_array['restaurant_id'] = $restaurant_id;
        $insert_array['title'] = $this->input->get_post('title');
        $insert_array['designation'] = $this->input->get_post('designation');


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
                $this->response([
                    'status' => FALSE,
                    'message' => $this->upload->display_errors()
                ], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
            }
        }

        $id = $this->restaurant_model->add_staff($insert_array);

        $this->data['status'] = True;
        $this->data['message'] = 'Staff added successfully!';
        $this->set_response($this->data, REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
    }

    public function update_post($id){
        $staff = $this->restaurant_model->get_staff_by_id($id);
        if(!$staff) {
            $this->response([
                'status' => FALSE,
                'message' => 'Staff not found.'
            ], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
        }
        $delete_image = $this->input->post('delete_image');

        $update_array['title'] = $this->input->post('title');
        $update_array['designation'] = $this->input->post('designation');

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

            $previous_image = $staff->image;
            delete_file('./'.UPLOADS.'/'.$previous_image);

        }
        elseif ($delete_image != null && $delete_image == 1){
            $previous_image = $staff->image;
            $update_array['image'] = '';
            delete_file('./'.UPLOADS.'/'.$previous_image);
        }

        # Update data into database
        $this->restaurant_model->update_staff($id,$update_array);
        $this->data['status'] = True;
        $this->data['message'] = 'Staff updated successfully!';
        $this->data['staff'] =  $this->restaurant_model->get_staff_by_id($id);

        $this->set_response($this->data, REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
    }

    public function delete_delete($id){
        $staff = $this->restaurant_model->get_staff_by_id($id);
        if(!$staff) {
            $this->response([
                'status' => FALSE,
                'message' => 'Staff not found.'
            ], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
        }

        $this->restaurant_model->delete_staff($id);
        $this->data['status'] = True;
        $this->data['message'] = 'Staff deleted successfully';

        $this->set_response($this->data, REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
    }
}
