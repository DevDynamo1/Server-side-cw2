<?php
// In your Attempt_model.php

class Attempt_model extends CI_Model {

    public function __construct(){
        parent::__construct();
    }

    public function insert_attempt($attemptData) {
        // Insert attempt data into the database
        $this->db->insert('attempt', $attemptData);

        // Check if the insert was successful
        if ($this->db->affected_rows() > 0) {
            // Return the ID of the inserted attempt
            return $this->db->insert_id();
        } else {
            // Return false if the insert failed
            return false;
        }
    }

}
