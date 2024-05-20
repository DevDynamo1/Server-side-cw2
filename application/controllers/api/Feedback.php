<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'libraries/REST_Controller.php';

class Feedback extends REST_Controller {

    public function __construct(){
        parent::__construct();
        $this->load->model('api/feedback_model');
    }

    // View for providing feedback on a quiz
    public function index_get($score = null, $categoryID = null){
        $data['score'] = $score;
        $data['categoryID'] = $categoryID;

        if ($this->session->userdata('logged_in')) {
            $this->load->view('quiz_feedback', $data);
        }else{
            show_404();
        }
    }

    // API endpoint for submitting feedback
    public function index_post() {
        // Get the raw POST data
        $rawData = $this->input->raw_input_stream;
        $postData = json_decode($rawData, true);

        // Check if json_decode was successful
        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->response(array(
                "status" => 0,
                "message" => "Invalid JSON"
            ), REST_Controller::HTTP_BAD_REQUEST);
            return;
        }

        // Collecting form data inputs
        $categoryId = isset($postData["CategoryID"]) ? $postData["CategoryID"] : null;
        $comment = isset($postData["Comment"]) ? $postData["Comment"] : null;
        $rate = isset($postData["Rating"]) ? $postData["Rating"] : null;


        // Form validation for inputs
        if (empty($categoryId) && (empty($comment) || empty($rate))) {
            $this->response(array(
                "status" => 0,
                "message" => "Category ID, comment or rate fields are required"
            ), REST_Controller::HTTP_BAD_REQUEST);
            return;
        }

        // Assuming you have a session variable for user_id
        $user_id = $this->session->userdata('user_id');
        $user_name= $this->session->userdata('user_name');
        // should add user name

        // All values are available, proceed with processing
        $feedbackData = array(
            "CategoryID" => $categoryId,
            "UserID" => $user_id,
            "Username"=> $user_name,
            "Comment" => $comment,
            "Rating"=> $rate
        );

        $attemptID = $this->feedback_model->insert_feedback($feedbackData);

        if ($attemptID) {
            $this->response(array(
                "status" => 1,
                "message" => "Attempt has been added",
                "AttemptID" => $attemptID
            ), REST_Controller::HTTP_OK);
        } else {
            $this->response(array(
                "status" => 0,
                "message" => "Failed to add the Attempt"
            ), REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    // API endpoint for fetching feedback by category ID
    public function feedback_get($category_id = null) {
        // Retrieve category ID from query parameters
        $category_id = $this->input->get('categoryId');

        // Validate that the category ID is provided
        if ($category_id === NULL) {
            $this->response([
                'status' => false,
                'message' => 'Category ID is required'
            ], REST_Controller::HTTP_BAD_REQUEST);
            return;
        }

        // Sanitize the category ID
        $category_id = $this->security->xss_clean($category_id);

        // Fetch feedback from the model
        $feedback = $this->feedback_model->get_feedback_by_category($category_id);

        // Check if feedback was found
        if ($feedback) {
            $this->response([
                'status' => true,
                'data' => $feedback
            ], REST_Controller::HTTP_OK);
        } else {
            $this->response([
                'status' => false,
                'message' => 'No questions found for the specified category ID'
            ], REST_Controller::HTTP_NOT_FOUND);
        }
    }

}