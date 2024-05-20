<?php

require APPPATH.'libraries/REST_Controller.php';

class Answer extends REST_Controller{

    public function __construct(){

        parent::__construct();
        //load database
        $this->load->model(array("api/answer_model"));
        $this->load->helper("security");
    }

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
        $answer = isset($postData["AnswerText"]) ? $this->security->xss_clean($postData["AnswerText"]) : null;
        $q_id = isset($postData["QuestionID"]) ? $this->security->xss_clean($postData["QuestionID"]) : null;
        $correct = isset($postData["IsCorrect"]) ? $this->security->xss_clean($postData["IsCorrect"]) : null;

        // Form validation for inputs
        $this->form_validation->set_rules("AnswerText", "Answer Text", "required");
        $this->form_validation->set_rules("QuestionID", "Question ID", "required");
        $this->form_validation->set_rules("IsCorrect", "IsCorrect", "required");


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
            $answer = array(
                "AnswerText" => $answer,
                "QuestionID" => $q_id,
                "IsCorrect" => $correct

            );

            if ($this->answer_model->insert_answer($answer)) {
                $this->response(array(
                    "status" => 1,
                    "message" => "Answer has been added"
                ), REST_Controller::HTTP_OK);
            } else {
                $this->response(array(
                    "status" => 0,
                    "message" => "Failed to add the Answer"
                ), REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
            }
        }
    }

    // Update the answers
    public function index_put(){
        $data = json_decode(file_get_contents("php://input"));

        if(isset($data->AnswerID) && isset($data->AnswerText ) && isset($data->IsCorrect)){

            $ans_id = $data->AnswerID;
            $ans_info = array(
                "AnswerText" => $data->AnswerText,
                "IsCorrect"=>$data->IsCorrect
            );

            if($this->answer_model->update_answer_information($ans_id, $ans_info)){

                $this->response(array(
                    "status" => 1,
                    "message" => "Answer data updated successfully"
                ), REST_Controller::HTTP_OK);
            }else{

                $this->response(array(
                    "status" => 0,
                    "messsage" => "Failed to update answer data"
                ), REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
            }
        }else{

            $this->response(array(
                "status" => 0,
                "message" => "All fields are needed"
            ), REST_Controller::HTTP_NOT_FOUND);
        }
    }

    // Delete answers
    public function index_delete(){
        $data = json_decode(file_get_contents("php://input"));
        $answer_id = $this->security->xss_clean($data->answer_id);

        if($this->answer_model->delete_answer($answer_id)){
            // retruns true
            $this->response(array(
                "status" => 1,
                "message" => "Answer has been deleted"
            ), REST_Controller::HTTP_OK);
        }else{
            // return false
            $this->response(array(
                "status" => 0,
                "message" => "Failed to delete answer"
            ), REST_Controller::HTTP_NOT_FOUND);
        }
    }

    public function get_post($q_id = null) {
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
        $q_id = isset($postData["QuestionID"]) ? $this->security->xss_clean($postData["QuestionID"]) : null;

        $this->form_validation->set_rules("QuestionID", "Question ID", "required");

        // Manually setting the data to validate
        $this->form_validation->set_data($postData);

        // Checking form submission for errors
        if ($this->form_validation->run() === FALSE) {
            $this->response([
                'status' => false,
                'message' => 'Question ID is required'
            ], REST_Controller::HTTP_BAD_REQUEST);
        }

        $answers = $this->answer_model->get_ans_by_question($q_id);

        if ($answers) {
            $this->response([
                'status' => true,
                'data' => $answers
            ], REST_Controller::HTTP_OK);
        } else {
            $this->response([
                'status' => false,
                'message' => 'No questions found for the specified category ID'
            ], REST_Controller::HTTP_NOT_FOUND);
        }
    }

}

?>
