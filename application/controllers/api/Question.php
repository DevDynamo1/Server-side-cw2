<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'libraries/REST_Controller.php';

class Question extends REST_Controller {

    public function __construct(){
        parent::__construct();
        $this->load->model('api/question_model');
    }

    // View for adding a new question
    public function add_get($categoryId = NULL) {
        // If $categoryId is NULL, try to get it from the query string
        if ($categoryId === NULL) {
            $categoryId = $this->input->get('categoryId');
        }

        // Check if $categoryId is still NULL
        if ($categoryId === NULL) {
            show_404(); // Show 404 if no category ID is provided
        }

        // Pass the category ID to the view
        $data['categoryId'] = $categoryId;

        if ($this->session->userdata('logged_in')) {
            // Load the add question view with the category ID
            $this->load->view('question_add', $data);
        }else{
            show_404();
        }
    }

    // Check if $categoryId is still NULL
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
        $question = isset($postData["QuestionText"]) ? $this->security->xss_clean($postData["QuestionText"]) : null;
        $cat_id = isset($postData["CategoryID"]) ? $this->security->xss_clean($postData["CategoryID"]) : null;

        // Form validation for inputs
        $this->form_validation->set_rules("QuestionText", "Question Text", "required");
        $this->form_validation->set_rules("CategoryID", "Category ID", "required");

        // Manually setting the data to validate
        $this->form_validation->set_data($postData);

        // Checking form submission for errors
        if ($this->form_validation->run() === FALSE) {
            // We have some errors
            $this->response(array(
                "status" => 0,
                "message" => validation_errors()
            ), REST_Controller::HTTP_BAD_REQUEST);
        } else {
            // All values are available
            $data = array(
                "QuestionText" => $question,
                "CategoryID" => $cat_id
            );

            // Insert question and get QuestionID
            // Insert question and get QuestionID
            $questionId = $this->question_model->insert_question($data);

            if ($questionId) {
                $this->response(array(
                    "status" => 1,
                    "message" => "Question has been added",
                    "QuestionID" => $questionId
                ), REST_Controller::HTTP_OK);
            } else {
                $this->response(array(
                    "status" => 0,
                    "message" => "Failed to add the Question"
                ), REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
            }
        }
    }

    // API endpoint for fetching questions by category ID
    public function get_post($category_id = null) {
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
        $category_id = isset($postData["CategoryID"]) ? $this->security->xss_clean($postData["CategoryID"]) : null;

        $this->form_validation->set_rules("CategoryID", "Category ID", "required");

        // Manually setting the data to validate
        $this->form_validation->set_data($postData);

        // Checking form submission for errors
        if ($this->form_validation->run() === FALSE) {
            $this->response([
                'status' => false,
                'message' => 'Category ID is required'
            ], REST_Controller::HTTP_BAD_REQUEST);
        }

        $questions = $this->question_model->get_questions_by_category($category_id);

        if ($questions) {
            $this->response([
                'status' => true,
                'data' => $questions
            ], REST_Controller::HTTP_OK);
        } else {
            $this->response([
                'status' => false,
                'message' => 'No questions found for the specified category ID'
            ], REST_Controller::HTTP_NOT_FOUND);
        }
    }
}
