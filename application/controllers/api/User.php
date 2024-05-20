<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';

class User extends REST_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model('api/User_model');
        $this->load->library('encryption');
        $this->load->config('config');
    }

    // Load login view
    public function login_get() {
        // Load the login view
        $this->load->view('login');
    }

    // Load registration view
    public function register_get() {
        // Load the registration view
        $this->load->view('register');
    }

    public function profile_view_get() {
        if ($this->session->userdata('logged_in')) {
            // Load the registration view
            $this->load->view('profile');
        }else{
            show_404();
        }
    }

    public function password_change_get() {
        if ($this->session->userdata('logged_in')) {

            // Load the registration view
            $this->load->view('password_change');
        }else{
            show_404();
        }
    }

    // API endpoint to add a new user
    public function add_user_post() {
        $email = $this->post('email');
        $password = $this->post('password');
        $username = $this->post('username'); // Ensure this matches the field name

        if (!empty($email) && !empty($password) && !empty($username)) {
            // Check if the email already exists
            if ($this->User_model->is_email_exists($email)) {
                $this->response(['status' => 'error', 'error_code' => 'EMAIL_EXISTS', 'message' => 'Email already exists'], REST_Controller::HTTP_BAD_REQUEST);
                return;
            }

            // Generate verification key and expiration time
            $verification_key = mt_rand(1000, 9999); // Generate a 6-digit random number
            $verification_key_expires_at = strtotime('+5 minutes'); // Set expiration to 5 minutes from now

            // Prepare user data
            $data['name'] = $username;
            $data['email'] = $email;
            $data['verification_key'] = $verification_key;
            $data['verification_key_expires_at'] = $verification_key_expires_at;

            // Attempt to add the user
            $result = $this->User_model->add_user($email, $password, $username, $verification_key, $verification_key_expires_at);
            if ($result !== false) {
                // Send email verification
                if ($this->sendEmail($data)) {
                    $this->response(['status' => 'success', 'message' => 'Check your Inbox to activate your account'], REST_Controller::HTTP_OK);
                } else {
                    $this->response(['status' => 'error', 'message' => 'Error sending an activation email'], REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
                }
            } else {
                $this->response(['status' => 'error', 'message' => 'Failed to add user'], REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
            }
        } else {
            $this->response(['status' => 'error', 'message' => 'Email, password, and username are required'], REST_Controller::HTTP_BAD_REQUEST);
        }
    }


    public function resend_put(){
        $email = $this->session->userdata('email');
        $user_id = $this->session->userdata('user_id');

        $verification_key = mt_rand(1000, 9999); // Generate a 6-digit random number
        $verification_key_expires_at = strtotime('+5 minutes'); // Set expiration to 5 minutes from now

        $data['name'] = $this->session->userdata('user_name');
        $data['email'] = $email;
        $data['verification_key'] = $verification_key;
        $data['verification_key_expires_at'] = $verification_key_expires_at;

        $result = $this->User_model->update_user($email, $verification_key, $verification_key_expires_at);
        if ($result !== false) {
            if ($this->sendEmail($data)){
                $this->response(['status' => 'success', 'message' => 'Check your Inbox to activate your account'], REST_Controller::HTTP_OK);
            }else{
                $this->response(['status' => 'success', 'message' => 'Error sending an activation email'], REST_Controller::HTTP_OK);
            }
        } else {
            $this->response(['status' => 'error', 'message' => 'Email, password, and confirm password are required'], REST_Controller::HTTP_BAD_REQUEST);
        }
    }

    // sending verification email with the authntication code
    private function sendEmail($data){
        $config = array(
            'useragent'=>'Codeigniter',
            'protocol'  => 'smtp',
            'mailpath'=>'usr/sbin/sendmail',
            'smtp_host' => 'ssl://smtp.gmail.com',
            'smtp_timeout' => 55,
            'smtp_port' => 465,
            'smtp_user'  => 'quizbuddies.1234@gmail.com',
            'smtp_pass'  => 'fgld bbgr nfob yamf', // gmail app password
            'mailtype'  => 'html',
            'charset' => 'iso-8859-1',
            'wordwrap' => TRUE,
            'wrapchars' =>76,
            'validate' =>FALSE,
            'priority' =>3,
            'crlf'=>"\r\n",
            'newline'=>"\r\n",
            'bcc_batch_mode'=>FALSE,
            'bcc_batch_size'=>200
        );

        $subject = "Please verify email for QuizBuddy login";
        $message = "<p>Hi ".$data['name']."</p>
			<p>This is email verification mail from QuizBuddy Login Register system. For complete registration process and login into system. Authentication code: ".$data['verification_key'].".</p>
			<p>Once you click this link your email will be verified and you can login into system.</p>
			<p>Thanks,</p>";

        $this->load->library('email',$config);
        $this->email->set_newline("\r\n");
        $this->email->from('info@quizbuddies.info');
        $this->email->to($data['email']);
        $this->email->subject($subject);
        $this->email->message($message);
        $this->email->set_mailtype('html');

        if($this->email->send()){
            return true;
        }else{
            return false;
        }

    }

    public function authcode_get() {
        if($this->session->userdata('user_id'))  {
        $this->load->view('email_verification');
        }else{
            show_404();
        }

    }
    public function logout_post() {
        $data = $this->session->all_userdata();
        foreach($data as $row => $rows_value)
        {
            $this->session->unset_userdata($row);
        }
        $this->load->view('login');
    }


    // API endpoint to authenticate user login
    public function login_post() {
        $email = $this->post('email');
        $password = $this->post('password');

        if (!empty($email) && !empty($password)) {
            $user = $this->User_model->login($email, $password);
            if ($user != false) {
                if ($user->is_email_verified == 'no'){
                    $session_data = array(
                        'user_id' => $user->id,
                        'email'=> $user-> email,
                        'user_name'=> $user-> UserName

                    );
                    $this->session->set_userdata($session_data);
                    $this->response(['status' => 'success', 'message' => 'Login successful', 'requiresAuthCode' => true], REST_Controller::HTTP_OK);
                } else{
                    $session_data = array(
                        'user_id' => $user->id,
                        'user_name'=> $user-> UserName,
                        'email'=> $user-> email,
                        'ip_address' => $_SERVER['REMOTE_ADDR'],
                        'login_timestamp' => time(),
                        'login_date' => date('Y-m-d H:i:s'),
                        'logged_in' => TRUE
                    );
                    // Set the session data
                    $this->session->set_userdata($session_data);
                    $this->response(['status' => 'success', 'message' => 'Login successful', 'requiresAuthCode' => false], REST_Controller::HTTP_OK);
                }

            } else {
                $this->response(['status' => 'error', 'message' => 'Invalid email or password'], REST_Controller::HTTP_UNAUTHORIZED);
            }
        } else {
            $this->response(['status' => 'error', 'message' => 'Email and password are required'], REST_Controller::HTTP_BAD_REQUEST);
        }
    }

    public function authenticate_post() {
        $code = $this->post('code');
        $email = $this->session->userdata('email');

        if (!empty($code) && !empty($email)) {
            $user = $this->User_model->authenticate($email, $code);
            if ($user !== false) {
                $session_data = array(
//                    'user_id' => $user->id,
//                    'user_name'=> $user->UserName,
                    'ip_address' => $_SERVER['REMOTE_ADDR'],
                    'login_timestamp' => time(),
                    'login_date' => date('Y-m-d H:i:s'),
                    'logged_in' => TRUE
                );
//                // Set the session data
                $this->session->set_userdata($session_data);
                $this->response(['status' => 'success', 'message' => 'Login successful'], REST_Controller::HTTP_OK);
            } else {
                $this->response(['status' => 'error', 'message' => 'Invalid verification code or email'], REST_Controller::HTTP_UNAUTHORIZED);
            }
        } else {
            $this->response(['status' => 'error', 'message' => 'Verification code and email are required'], REST_Controller::HTTP_BAD_REQUEST);
        }
    }



    // Example API endpoint requiring logged-in status
    public function protected_endpoint_get() {
        // Check session for user data
        $user_data = $this->session->userdata('user_data');
        if (!empty($user_data)) {
            // User is logged in, perform action
            $this->response(['status' => 'success', 'message' => 'This is a protected endpoint'], REST_Controller::HTTP_OK);
        } else {
            // User is not logged in, redirect to login page or handle accordingly
            $this->response(['status' => 'error', 'message' => 'User not logged in'], REST_Controller::HTTP_UNAUTHORIZED);
        }
    }

    // API endpoint to get user profile
    public function get_profile_get() {
        // Get the user_id from the session
        $user_id = $this->session->userdata('user_id');

        if (!empty($user_id)) {
            $user_profile = $this->User_model->get_profile($user_id);
            if ($user_profile) {
                $this->response(['status' => 'success', 'data' => $user_profile], REST_Controller::HTTP_OK);
            } else {
                $this->response(['status' => 'error', 'message' => 'User profile not found'], REST_Controller::HTTP_NOT_FOUND);
            }
        } else {
            $this->response(['status' => 'error', 'message' => 'User ID not found in session'], REST_Controller::HTTP_BAD_REQUEST);
        }
    }


// API endpoint to delete user
    public function delete_user_delete() {
        $email = $this->delete('email');

        if (!empty($email)) {
            if ($this->User_model->delete_user($email)) {
                $this->response(['status' => 'success', 'message' => 'User deleted successfully'], REST_Controller::HTTP_OK);
            } else {
                $this->response(['status' => 'error', 'message' => 'Failed to delete user'], REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
            }
        } else {
            $this->response(['status' => 'error', 'message' => 'Email is required'], REST_Controller::HTTP_BAD_REQUEST);
        }
    }

    // API endpoint to update user's password
    public function update_password_put() {
        $user_id = $this->put('id');
        $current_password = $this->put('current_password');
        $new_password = $this->put('new_password');
        $confirm_new_password = $this->put('confirm_new_password');
        $email = $this->session->userdata('email');


        if (!empty($user_id) && !empty($current_password) && !empty($new_password) && !empty($confirm_new_password)) {
            if ($this->User_model->login($email, $current_password)) {
                $result = $this->User_model->update_password($user_id, $new_password);
                if ($result) {
                    $this->response(['status' => 'success', 'message' => 'Password updated successfully'], REST_Controller::HTTP_OK);
                } else {
                    $this->response(['status' => 'error', 'message' => 'Failed to update password'], REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
                }
            } else {
                $this->response(['status' => 'error', 'message' => 'New passwords do not match'], REST_Controller::HTTP_BAD_REQUEST);
            }
        } else {
            $this->response(['status' => 'error', 'message' => 'User ID, current password, new password, and confirm new password are required'], REST_Controller::HTTP_BAD_REQUEST);
        }
    }

    // User controller

// Fetch user profile data based on user ID
    public function profile_get($id) {
        // Check if the user ID is provided
        if (!empty($id)) {
            // Fetch user data from the database based on the provided user ID
            $user = $this->User_model->get_user_by_id($id);

            if ($user) {
                // Return user profile data
                $profile_data = array(
                    'email' => $user->email,
                    'created_at' => $user->created_at // Assuming this is the created date field
                );
                $this->response(['status' => 'success', 'profile' => $profile_data], REST_Controller::HTTP_OK);
            } else {
                // User not found
                $this->response(['status' => 'error', 'message' => 'User not found'], REST_Controller::HTTP_NOT_FOUND);
            }
        } else {
            // User ID not provided
            $this->response(['status' => 'error', 'message' => 'User ID is required'], REST_Controller::HTTP_BAD_REQUEST);
        }
    }

    public function index_delete() {
        // Get user ID from the session or request data
        $user_id = $this->session->userdata('user_id'); // Adjust as needed based on your session setup

        // Delete the user account
        $result = $this->User_model->delete_user($user_id);

        // Handle the result accordingly (e.g., return JSON response)
        if ($result) {
            // User account deleted successfully
            $this->output->set_content_type('application/json')->set_output(json_encode(array('success' => true)));
        } else {
            // Failed to delete user account
            $this->output->set_content_type('application/json')->set_output(json_encode(array('success' => false, 'message' => 'Failed to delete user account.')));
        }
    }

}
