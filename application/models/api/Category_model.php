<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Category_model extends CI_Model {

    public function __construct(){
        parent::__construct();
    }

    // Search categories by tag name or category name
    public function search_categories($search){
        // Perform a SQL join to search both tag names and category names
        $this->db->select('Category.CategoryID, Category.CategoryName, Category.Level, Category.Visibility');
        $this->db->from('Tag');
        $this->db->join('Category', 'Tag.CategoryID = Category.CategoryID');
        $this->db->group_by('Category.CategoryID'); // Group by CategoryID to avoid duplicate categories

        // Escape the search term to prevent SQL injection
        $escaped_search = $this->db->escape($search);

        // Use SOUNDEX to handle similar sounding words for better matching
        $this->db->group_start();
        $this->db->like('Tag.tagname', $search);
        $this->db->or_like('Category.CategoryName', $search);
        $this->db->or_where('SOUNDEX(Tag.tagname) = SOUNDEX(' . $escaped_search . ')');
        $this->db->or_where('SOUNDEX(Category.CategoryName) = SOUNDEX(' . $escaped_search . ')');
        $this->db->group_end();

        $query = $this->db->get();
        $results = $query->result();

        return $results;
    }

    // Calculate the similarity ratio between two strings using the Levenshtein distance
    private function levenshtein_ratio($str1, $str2) {
        $lev = levenshtein($str1, $str2);
        $max_len = max(strlen($str1), strlen($str2));
        return ($max_len - $lev) / $max_len;
    }

    // Insert a new category into the database
    public function insert_category($data = array()){
        $this->db->insert("Category", $data);
        return $this->db->insert_id(); // This returns the last inserted ID
    }

    public function get_category_by_id($categoryID) {
        $this->db->where('CategoryID', $categoryID);
        $query = $this->db->get('Category');
        return $query->row_array();
    }

    // Update category information
    public function update_category_info($categoryID, $level, $visibility, $pin = null) {
        $data = array(
            'Level' => $level,
            'Visibility' => $visibility
        );

        // Include PIN if provided and visibility is private
        if ($pin !== null && $visibility === 'private') {
            $data['PIN'] = $pin;
        }

        $this->db->where('CategoryID', $categoryID);
        return $this->db->update('Category', $data);
    }


    // Delete a category and its related records from the database
    public function delete_category($categoryId) {
        // Check if the category belongs to the authenticated user
        $user_id = $this->session->userdata('user_id');

        $category = $this->db->get_where('Category', array('CategoryID' => $categoryId, 'UserID' => $user_id))->row();

        if (!$category) {
            // Category does not exist or does not belong to the user, return false or throw an exception
            return false;
        }

        // First, delete related records in the `bookmark` table
        $this->db->where('CategoryID', $categoryId);
        $this->db->delete('bookmark');

        // Delete related records from other tables (Tag, Attempts, Feedback, Questions)
        $this->db->where('CategoryID', $categoryId);
        $this->db->delete('Tag');

        $this->db->where('CategoryID', $categoryId);
        $this->db->delete('Attempt');

        $this->db->where('CategoryID', $categoryId);
        $this->db->delete('Feedback');

        // First, get the QuestionIDs associated with the CategoryID
        $this->db->select('QuestionID');
        $this->db->where('CategoryID', $categoryId);
        $questionIds = $this->db->get('Questions')->result_array();

        // Delete answers associated with each QuestionID
        foreach ($questionIds as $questionId) {
            $this->db->where('QuestionID', $questionId['QuestionID']);
            $this->db->delete('Answers');
        }

        // Delete questions associated with the CategoryID
        $this->db->where('CategoryID', $categoryId);
        $this->db->delete('Questions');

        // Finally, delete the category record itself
        $this->db->where('CategoryID', $categoryId);
        $this->db->delete('Category');

        // Return true or some other success indicator
        return true;
    }




}
