<?php
// In your Attempt_model.php

class Bookmark_model extends CI_Model {

    public function __construct(){
        parent::__construct();
    }

    // Insert bookmark data into the database
    public function insert_bookmark($bookmark) {
        $this->db->insert('Bookmark', $bookmark);

        // Check if the insert was successful
        if ($this->db->affected_rows() > 0) {
            // Return the ID of the inserted attempt
            return $this->db->insert_id();
        } else {
            // Return false if the insert failed
            return false;
        }
    }

    public function delete_bookmark($bookmark_id) {
        $this->db->where('id', $bookmark_id);
        $this->db->delete('Bookmark');

        // Check if the deletion was successful
        return $this->db->affected_rows() > 0;
    }

}
