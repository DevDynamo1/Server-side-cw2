<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'libraries/REST_Controller.php';

class Category extends REST_Controller {

    public function __construct(){
        parent::__construct();
        $this->load->model('api/category_model');
    }

    // API endpoint for adding a new category
    public function index_post() {
        // retrieve user id from the session
        $user_id = $this->session->userdata('user_id');
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
        $categoryName = isset($postData["CategoryName"]) ? $this->security->xss_clean($postData["CategoryName"]) : null;
        $description = isset($postData["Description"]) ? $this->security->xss_clean($postData["Description"]) : null;

        // Form validation for inputs
        $this->form_validation->set_rules("CategoryName", "Category Name", "required");
        $this->form_validation->set_rules("Description", "Description", "max_length[255]");

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
            $category = array(
                "CategoryName" => $categoryName,
                "Description" => $description,
                "UserID"=> $user_id
            );

            $catergoryID = $this->category_model->insert_category($category);

            if ($catergoryID) {
                $this->response(array(
                    "status" => 1,
                    "message" => "Category has been added",
                    "CategoryID"=>$catergoryID
                ), REST_Controller::HTTP_OK);
            } else {
                $this->response(array(
                    "status" => 0,
                    "message" => "Failed to add the Category"
                ), REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
            }
        }
    }

    // View for listing categories
    public function index_get(){
        if ($this->session->userdata('logged_in')) {
            $this->load->view('category');
        }else{
            show_404();
        }
    }

    // View for adding a category
    public function add_get(){
        if ($this->session->userdata('logged_in')) {
            $this->load->view('category_add');
        }else{
            show_404();
        }
    }

    // API endpoint for searching categories
    public function search_get(){
        $search = $this->get('search');
        $data['categories'] = $this->category_model->search_categories($search);
        $this->response($data, REST_Controller::HTTP_OK);

    }

    // API endpoint for filtering categories
    public function filter_get(){
        $search = $this->get('filter');
        $data['categories'] = $this->category_model->search_categories($search);
        $this->response($data, REST_Controller::HTTP_OK);

    }

    // API endpoint for updating category level
    public function level_put() {
        // Get the raw PUT data
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
        $categoryID = isset($postData["CategoryID"]) ? $postData["CategoryID"] : null;
        $level = isset($postData["Level"]) ? $postData["Level"] : null;
        $visibility = isset($postData["Visibility"]) ? $postData["Visibility"] : "Public";
        $pin = isset($postData["PIN"]) ? $postData["PIN"] : null; // Added line to get PIN


        // Form validation for inputs
        $this->form_validation->set_data($postData);
        $this->form_validation->set_rules("CategoryID", "Category ID", "required|integer");
        $this->form_validation->set_rules("Level", "Level", "required");
        $this->form_validation->set_rules("Visibility", "Visibility");

        // Check PIN only if visibility is private
        if ($visibility === "private") {
            $this->form_validation->set_rules("PIN", "PIN", "required"); // Validate PIN only if visibility is private
        }

        // Checking form submission for errors
        if ($this->form_validation->run() === FALSE) {
            // We have some errors
            $this->response(array(
                "status" => 0,
                "message" => validation_errors()
            ), REST_Controller::HTTP_BAD_REQUEST);
        } else {
            // All values are available
            // Check if the category exists
            $existingCategory = $this->category_model->get_category_by_id($categoryID);

            if (!$existingCategory) {
                $this->response(array(
                    "status" => 0,
                    "message" => "Category not found"
                ), REST_Controller::HTTP_NOT_FOUND);
                return;
            }

            // Update the level
            $updated = $this->category_model->update_category_info($categoryID, $level, $visibility, $pin);

            if ($updated) {
                $this->response(array(
                    "status" => 1,
                    "message" => "Category level has been updated"
                ), REST_Controller::HTTP_OK);
            } else {
                $this->response(array(
                    "status" => 0,
                    "message" => "Failed to update the category level"
                ), REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
            }
        }
    }

    // REST API endpoint to delete a category and its related records
    public function index_delete($category_id) {
        // Check if the user is authenticated and authorized to perform this action
        if (!$this->session->userdata('logged_in')) {
            $this->response(['message' => 'Unauthorized'], REST_Controller::HTTP_UNAUTHORIZED);
            return;
        }
        $deleted=$this->category_model->delete_category($category_id);
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
