<?php

class Answer_model extends CI_Model{

    public function __construct(){
        parent::__construct();
    }

    // Retrieve all answers from the database
    public function get_answers(){

        return $this->db->get("Answers")->result();

    }

    // Insert a new answer into the database
    public function insert_answer($data = array()){

        return $this->db->insert("Answers", $data);
    }

    // Delete an answer from the database based on its ID
    public function delete_answer($answer_id){

        // delete method
        $this->db->where("AnswerID", $answer_id);
        return $this->db->delete("Answers");
    }

    // Update answer information in the database
    public function update_answer_information($id, $information){

        $this->db->where("AnswerID", $id);
        return $this->db->update("Answers", $information);
    }

    // Retrieve answers for a specific question from the database
    public function get_ans_by_question($q_id){

        $this->db->where("QuestionID", $q_id);
        return $this->db->get("Answers")->result();
    }
}

?>
