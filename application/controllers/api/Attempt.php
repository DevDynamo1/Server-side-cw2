<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'libraries/REST_Controller.php';

class Attempt extends REST_Controller {

    public function __construct(){
        parent::__construct();
        $this->load->model('api/attempt_model');
    }

    public function welcome_get($categoryId = NULL, $categoryName=NULL) {
        if ($categoryId === NULL || $categoryName === NULL) {
            $categoryId = $this->input->get('categoryId');
            $categoryName =  $this->input->get('categoryName');
        }

        if ($categoryId === NULL || $categoryName === NULL) {
            show_404(); // Show 404 if no category ID is provided
        }

        // Pass the category ID to the view
        $data['categoryId'] = $categoryId;
        $data['categoryName'] = $categoryName;

        if ($this->session->userdata('logged_in')) {

            // Load the add question view with the category ID
            $this->load->view('quiz_welcome', $data);
        }else{
            show_404();
        }
    }

    public function index_post($categoryId = NULL) {
        // Get the raw POST data
        $rawData = $this->input->raw_input_stream;
        $postData = json_decode($rawData, true);

        // Check if json_decode was successful
        if ($postData === null && json_last_error() !== JSON_ERROR_NONE) {
            $this->response(array(
                "status" => 0,
                "message" => "Invalid JSON"
            ), REST_Controller::HTTP_BAD_REQUEST);
            return;
        }

        // Collecting form data inputs
        $categoryId = isset($postData["CategoryID"]) ? intval($postData["CategoryID"]) : null;
        $score = isset($postData["Score"]) ? intval($postData["Score"]) : null;

        // Form validation for inputs
        if ($categoryId === null || $score === null) {
            // If either Category ID or Score is empty, return an error response
            $this->response(array(
                "status" => 0,
                "message" => "Category ID and Score fields are required"
            ), REST_Controller::HTTP_BAD_REQUEST);
            return;
        }

        // retrieve user id from the session
        $user_id = $this->session->userdata('user_id');

        $attemptData = array(
            "CategoryID" => $categoryId,
            "UserID" => $user_id,
            "Score" => $score
        );

        $attemptID = $this->attempt_model->insert_attempt($attemptData);

        if ($attemptID) {
            $this->response(array(
                "status" => 1,
                "message" => "Attempt has been added",
                "AttemptID" => $attemptID,
                "Score" => $score
            ), REST_Controller::HTTP_OK);
        } else {
            $this->response(array(
                "status" => 0,
                "message" => "Failed to add the Attempt"
            ), REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
        }
    }




}