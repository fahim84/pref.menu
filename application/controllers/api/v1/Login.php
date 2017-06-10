<?php

defined('BASEPATH') OR exit('No direct script access allowed');

// This can be removed if you use __autoload() in config.php OR use Modular Extensions
require APPPATH . '/libraries/REST_Controller.php';


class Login extends REST_Controller {

    function __construct()
    {
        // Construct the parent class
        parent::__construct();
        $this->load->model('login_model');

    }

    public function index_post()
    {
        $email = $this->input->get_post('email');
        $password = $this->input->get_post('password');
        if(!$email || !$password){
            // Set the response and exit
            $this->response([
                'status' => FALSE,
                'message' => 'Wrong Parameters'
            ], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
        }
        $login_detail['email'] = $email;
        $login_detail['password'] = md5($password);
        if($user_detail = $this->login_model->check_login($login_detail)) { // If Login process success
            if($user_detail['deleted'])
            {
                // Set the response and exit
                $this->response([
                    'status' => FALSE,
                    'message' => 'Your account is banned by administration...'
                ], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
            }
            if($user_detail['is_activated'])
            {
                $this->login_model->update_user($user_detail['id'],array('last_login' => date('Y-m-d H:i:s')));
                $user_detail = $this->login_model->get_user_by_id($user_detail['id']);
                $this->data['status'] = TRUE;
                $this->data['user'] = $user_detail;
            }   else {
                // Set the response and exit
                $this->response([
                    'status' => FALSE,
                    'message' => 'Your account is not yet activated. If you have not received an email confirmation within 24 hours please email us on: contact@pref.menu'
                ], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
            }

        } else {
            // Set the response and exit
            $this->response([
                'status' => FALSE,
                'message' => 'Incorrect email or password'
            ], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
        }
        $this->set_response($this->data, REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
    }

    public function signup_post(){

        $registration_detail['business_type'] = $this->input->get_post('business_type');
        $registration_detail['name'] = $this->input->get_post('name');
        $registration_detail['manager_name'] = $this->input->get_post('manager_name');
        $registration_detail['address'] = $this->input->get_post('address');
        $registration_detail['phone'] = $this->input->get_post('phone');
        $registration_detail['email'] = $this->input->get_post('email');
        $registration_detail['password'] = md5($this->input->get_post('password'));
        $registration_detail['report_password'] = md5($this->input->get_post('password'));
        $registration_detail['ordering_feature'] = 0;
        $registration_detail['is_activated'] = 0;

        if($this->login_model->email_already_exists($registration_detail['email']))
        {
            // Set the response and exit
            $this->response([
                'status' => FALSE,
                'message' => 'Your email is already exists....'
            ], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
        }
        # File uploading configuration
        $config['upload_path'] = './'.UPLOADS.'/';
        $config['allowed_types'] = 'gif|jpg|png|jpeg';
        $config['encrypt_name'] = true;

        $password = $this->input->get_post('password');

        $this->load->library('upload', $config);
        if ($this->upload->do_upload('logo'))
        {
            # Get uploading detail here
            $upload_detail = $this->upload->data();

            $registration_detail['logo'] = $upload_detail['file_name'];
            $logo = $registration_detail['logo'];

            # Get width and height of uploaded file
            $image_path = './'.UPLOADS.'/'.$logo;
            $width = get_width($image_path);
            $height = get_height($image_path);

            # Resize Image Now
            $width > RESIZE_IF_PIXELS_LIMIT ? resize_image2($image_path, RESIZE_IMAGE_WIDTH, '', 'W') : '';
            $height > RESIZE_IF_PIXELS_LIMIT ? resize_image2($image_path, '', RESIZE_IMAGE_HEIGHT, 'H') : '';

        }
        else
        {
            $uploaded_file_array = (isset($_FILES['logo']) and $_FILES['logo']['size'] > 0 and $_FILES['logo']['error'] == 0) ? $_FILES['logo'] : '';
            # Show uploading error only when the file uploading attempt exist.
            if( is_array($uploaded_file_array) ) {
                // Set the response and exit
                $this->response([
                    'status' => FALSE,
                    'message' => $this->upload->display_errors()
                ], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
            }
        }

        if($new_user_id = $this->login_model->signup($registration_detail)) {

            $activation_link = base_url() . 'login/activate_account/' . md5($new_user_id);
            //$activation_link = '<a href="'.$activation_link.'">'.$activation_link.'</a>';
            $message_for_signup_user = "Hello {$registration_detail['manager_name']}, <p>Thank you for signing up with Pref.menu. You will receive an email approval notification shortly.</p>";

            $subject = $registration_detail['name'] . ' registration request.';

            $logo_path = base_url() . UPLOADS . "/" . @$logo;

            $ordering_feature = $registration_detail['ordering_feature'] ? 'Ordering and Feedback' : 'Feedback Only';

            $EmailMsg = '<table cellpadding="5" cellspacing="2" border="1">';
            $EmailMsg .= '<tr>';
            $EmailMsg .= '<td align="left">Business Type<td>';
            $EmailMsg .= '<td align="left">' . $registration_detail['business_type'] . '<td>';
            $EmailMsg .= '</tr>';
            $EmailMsg .= '<tr>';
            $EmailMsg .= '<td align="left">Business Name<td>';
            $EmailMsg .= '<td align="left">' . $registration_detail['name'] . '<td>';
            $EmailMsg .= '</tr>';
            $EmailMsg .= '<tr>';
            $EmailMsg .= '<td align="left">Using Pref for<td>';
            $EmailMsg .= '<td align="left">' . $ordering_feature . '<td>';
            $EmailMsg .= '</tr>';
            $EmailMsg .= '<tr>';
            $EmailMsg .= '<td align="left">Manager Name<td>';
            $EmailMsg .= '<td align="left">' . $registration_detail['manager_name'] . '<td>';
            $EmailMsg .= '</tr>';
            $EmailMsg .= '<tr>';
            $EmailMsg .= '<td align="left">Address<td>';
            $EmailMsg .= '<td align="left">' . $registration_detail['address'] . '<td>';
            $EmailMsg .= '</tr>';
            $EmailMsg .= '<tr>';
            $EmailMsg .= '<td align="left">Phone<td>';
            $EmailMsg .= '<td align="left">' . $registration_detail['phone'] . '<td>';
            $EmailMsg .= '</tr>';
            $EmailMsg .= '<tr>';
            $EmailMsg .= '<td align="left">Email<td>';
            $EmailMsg .= '<td align="left">' . $registration_detail['email'] . '<td>';
            $EmailMsg .= '</tr>';
            $EmailMsg .= '<tr>';
            $EmailMsg .= '<td align="left">Password<td>';
            $EmailMsg .= '<td align="left">' . $password . '<td>';
            $EmailMsg .= '</tr>';
            $EmailMsg .= '<tr>';
            $EmailMsg .= '<td align="left">Join Date<td>';
            $EmailMsg .= '<td align="left">' . date('Y-m-d') . '<td>';
            $EmailMsg .= '</tr>';
            $EmailMsg .= '<tr>';
            $EmailMsg .= '<td align="left">Logo<td>';
            $EmailMsg .= '<td align="left"><img title="' . $logo . '" src="' . $logo_path . '" alt="' . $logo . '" /><td>';
            $EmailMsg .= '</tr>';
            $EmailMsg .= '</table><br /><br />';
            $EmailMsg .= '<a href="' . $activation_link . '">Click here to Approve</a>';

            $this->load->library('email');

            # Send email to Administrator
            $this->email->clear(TRUE);
            $this->email->set_mailtype("html");
            $this->email->from(SYSTEM_EMAIL, SYSTEM_NAME);
            $this->email->reply_to($registration_detail['email'], $registration_detail['manager_name']);
            $this->email->to(ADMIN_EMAIL);
            $this->email->subject($subject);
            $this->email->message(get_email_message_with_wrapper($EmailMsg));
            $this->email->send();

            # Send email to Signup User
            $this->email->clear(TRUE);
            $this->email->set_mailtype("html");
            $this->email->from(SYSTEM_EMAIL, SYSTEM_NAME);
            $this->email->to($registration_detail['email']);
            $this->email->subject($registration_detail['name'] . ' registration request received.');
            $this->email->message(get_email_message_with_wrapper($message_for_signup_user));
            $this->email->send();
            //unset($registration_detail['password']);
            //unset($registration_detail['report_password']);
            //$registration_detail = array_merge(array('id' => $new_user_id), $registration_detail);
            //$registration_detail['id'] = $new_user_id;
            $this->data['status'] = True;
            $this->data['message'] = 'Thanks for signing up with Pref.menu. You will receive an email approval notification shortly.';
            $this->set_response($this->data, REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
        } else {
            // Set the response and exit
            $this->response([
                'status' => FALSE,
                'message' => 'Something went wrong. Could not register'
            ], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
        }

    }
}
