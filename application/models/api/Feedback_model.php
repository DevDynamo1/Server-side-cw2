<?php

class Feedback_model extends CI_Model{

    public function __construct(){
        parent::__construct();
    }

    public function insert_feedback($data = array()){
        $this->db->insert("Feedback", $data);
        return $this->db->insert_id(); // This returns the last inserted ID
    }

    public function get_feedback_by_category($category_id){

        $this->db->where("CategoryID", $category_id);
        return $this->db->get("Feedback")->result();
    }
}

?>