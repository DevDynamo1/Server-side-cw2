<?php
class Quiz_model extends CI_Model {

    public function get_pin_by_category_id($categoryId) {
        $this->db->select('PIN');
        $this->db->where('CategoryID', $categoryId);
        $query = $this->db->get('Category');

        if ($query->num_rows() == 1) {
            $row = $query->row();
            return $row->PIN;
        } else {
            return false; // Category or PIN not found
        }
    }

}
