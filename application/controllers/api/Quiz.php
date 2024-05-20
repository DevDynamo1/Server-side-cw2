<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'libraries/REST_Controller.php';

class Quiz extends REST_Controller {
    public function __construct(){
        parent::__construct();
        $this->load->model('api/quiz_model');

    }

    // View for taking a quiz
    public function index_get($categoryId = NULL) {
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
            $this->load->view('take_quiz', $data);
        }else{
            show_404();
        }
    }

    // View for displaying quiz welcome message
    public function welcome_get() {
        // Retrieve categoryId, categoryName, and visibility from the query string
        $categoryId = $this->input->get('categoryId');
        $categoryName = $this->input->get('categoryName');
        $visibility = $this->input->get('visibility');

        // Check if categoryId or categoryName is missing
        if ($categoryId === NULL || $categoryName === NULL) {
            show_404(); // Show 404 if any parameter is missing
        }

        // Pass the category ID, name, and visibility to the view
        $data['categoryId'] = $categoryId;
        $data['categoryName'] = $categoryName;
        $data['visibility'] = $visibility;

        // Check if the user is logged in
        if ($this->session->userdata('logged_in')) {
            // Load the welcome view with the category details
            $this->load->view('quiz_welcome', $data);
        } else {
            // If the user is not logged in, redirect to the login page
            redirect('login'); // Change 'login' to your actual login page route
        }
    }

    // API endpoint for PIN authentication
    public function authenticate_pin_post() {
        // Retrieve categoryId and PIN from the POST data
        $categoryId = $this->input->post('categoryId');
        $pin = $this->input->post('pin');

        // Validate input
        if (empty($categoryId) || empty($pin)) {
            $this->response(['success' => false, 'message' => 'Category ID and PIN are required.'], REST_Controller::HTTP_BAD_REQUEST);
        }

        // Perform PIN authentication (replace this with your own logic)
        // For demonstration purposes, let's assume the PIN is stored in the database
        $storedPin = $this->quiz_model->get_pin_by_category_id($categoryId);

        if ($pin === $storedPin) {
            $this->response(['success' => true, 'message' => 'PIN authenticated successfully.'], REST_Controller::HTTP_OK);
        } else {
            $this->response(['success' => false, 'message' => 'Invalid PIN.'], REST_Controller::HTTP_UNAUTHORIZED);
        }
    }

}