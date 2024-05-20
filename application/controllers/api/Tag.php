<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'libraries/REST_Controller.php';

class Tag extends REST_Controller {
    public function __construct(){
        parent::__construct();
        $this->load->model('api/tag_model');
    }

    // View for adding tags to a quiz
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
            $this->load->view('quiz_tag', $data);
        }else{
            show_404();
        }
    }

    // API endpoint for adding a tag to a quiz
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
        $tag = isset($postData["tagname"]) ? $this->security->xss_clean($postData["tagname"]) : null;
        $cat_id = isset($postData["CategoryID"]) ? $this->security->xss_clean($postData["CategoryID"]) : null;

        // Form validation for inputs
        $this->form_validation->set_rules("tagname", "tag name", "required");
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
                "tagname" => $tag,
                "CategoryID" => $cat_id
            );

            // Insert question and get QuestionID
            // Insert question and get QuestionID
            $TagID = $this->tag_model->insert_tag($data);

            if ($TagID) {
                $this->response(array(
                    "status" => 1,
                    "message" => "Tag has been added",
                    "TagID" => $TagID
                ), REST_Controller::HTTP_OK);
            } else {
                $this->response(array(
                    "status" => 0,
                    "message" => "Failed to add the tag"
                ), REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
            }
        }
    }

}