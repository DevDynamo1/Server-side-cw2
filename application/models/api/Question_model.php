<?php

class Question_model extends CI_Model{

    public function __construct(){
        parent::__construct();
    }

    // View method to load the add question view with the specified category ID
    public function view_get ($categoryId) {
        // Pass the category ID to the view
        $data['categoryId'] = $categoryId;

        // Load the add question view with the category ID
        $this->load->view('add_question_view', $data);
    }

    // Get questions by category ID
    public function get_questions_by_category($category_id){

        $this->db->where("CategoryID", $category_id);
        return $this->db->get("Questions")->result();
    }

    // Get all questions
    public function get_questions(){

        return $this->db->get("Questions")->result();

    }

    // Insert a new question into the database
    public function insert_question($data = array()){
        $this->db->insert("Questions", $data);
        return $this->db->insert_id(); // This returns the last inserted ID
    }

    // Delete a question from the database
    public function delete_question($question_id){

        // delete method
        $this->db->where("QuestionID", $question_id);
        return $this->db->delete("Questions");
    }

    // Update question information in the database
    public function update_question_information($id, $information){

        $this->db->where("QuestionID", $id);
        return $this->db->update("Questions", $information);
    }
}

?>
