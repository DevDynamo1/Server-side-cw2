<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'libraries/REST_Controller.php';

class Bookmark extends REST_Controller {

    public function __construct(){
        parent::__construct();
        $this->load->model('api/bookmark_model');
    }

    public function index_post($categoryId = NULL) {

        // Get the raw POST data
        $rawData = $this->input->raw_input_stream;
        $postData = json_decode($rawData, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->response(array(
                "status" => 0,
                "message" => "Invalid JSON"
            ), REST_Controller::HTTP_BAD_REQUEST);
            return;
        }

        // Collecting form data inputs
        $categoryId = isset($postData["categoryId"]) ? $postData["categoryId"] : null;

        // Form validation for inputs
        if (empty($categoryId)) {
            $this->response(array(
                "status" => 0,
                "message" => "Category ID is required"
            ), REST_Controller::HTTP_BAD_REQUEST);
            return;
        }

        $user_id = $this->session->userdata('user_id');

        $bookmark = array(
            "CategoryID" => $categoryId,
            "UserID" => $user_id
        );

        $bookmarkID = $this->bookmark_model->insert_bookmark($bookmark);

        if ($bookmarkID) {
            $this->response(array(
                "status" => 1,
                "message" => "Bookmark has been added",
                "BookmarkID" => $bookmarkID,
            ), REST_Controller::HTTP_OK);
        } else {
            $this->response(array(
                "status" => 0,
                "message" => "Failed to add the Attempt"
            ), REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function index_delete($category_id) {
        // Check if the user is authenticated and authorized to perform this action
        if (!$this->session->userdata('logged_in')) {
            $this->response(['message' => 'Unauthorized'], REST_Controller::HTTP_UNAUTHORIZED);
            return;
        }
        $deleted=$this->bookmark_model->delete_bookmark($category_id);
        // Attempt to delete the category and its related records
        if ($deleted) {
            // Category successfully deleted, return success response
            $this->response(array(
                "status" => 0,
                "message" => "Successfully Deleted"
            ), REST_Controller::HTTP_OK);
        } else {
            // Failed to delete the category, return error response
            $this->response(array(
                "status" => 0,
                "message" => "Failed to delete the category "
            ), REST_Controller::HTTP_INTERNAL_SERVER_ERROR);        }
    }


}