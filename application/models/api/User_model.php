<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User_model extends CI_Model {

    // Function to add a new user to the database
    public function add_user($email, $password, $username, $verification_key, $verification_key_expires_at) {
        // Hash the password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Format the expiration timestamp as MySQL DATETIME
        $expiration_datetime = date('Y-m-d H:i:s', $verification_key_expires_at);

        // Prepare data for insertion
        $data = array(
            'UserName' => $username,
            'email' => $email,
            'password' => $hashed_password,
            'verification_key' => $verification_key,
            'verification_key_expires_at' => $expiration_datetime,
            'is_email_verified'  => 'no'

        );

        // Insert user data into the database
        $this->db->insert('User', $data);

        // Check if the insertion was successful
        if ($this->db->affected_rows() > 0) {
            return true; // User added successfully
        } else {
            return false; // Failed to add user
        }
    }




    // Function to authenticate user login
    public function login($email, $password) {
        $this->db->where('email', $email);
        $user = $this->db->get('User')->row();

        if (!empty($user) && password_verify($password, $user->password)) { // Access properties with -> instead of []
            return $user;
        } else {
            return false;
        }
    }

    public function is_email_exists($email) {
        $this->db->where('email', $email);
        $query = $this->db->get('User');
        return $query->num_rows() > 0;
    }


    // Function to authenticate user login
//    public function authenticate($userid, $code) {
//        $this->db->where('id', $userid);
//        $user = $this->db->get('User')->row();
//
//        if (!empty($user) && $code == $user->verification_key) { // Access properties with -> instead of []
//            $data = array(
//                'is_email_verified'  => 'yes'
//            );
//            $this->db->where('id', $userid);
//            $this->db->update('User', $data);
//            return $user;
//        } else {
//            return false;
//        }
//    }

    public function authenticate($email, $code) {
        $this->db->where('email', $email);
        $user = $this->db->get('User')->row();

        if (!empty($user) && $code == $user->verification_key) {
            // Check if verification key is expired
            if ($user->verification_key_expires_at >= time()) {
                $data = array(
                    'is_email_verified' => 'yes'
                );
                $this->db->where('email', $email);
                $this->db->update('User', $data);
                return $user;
            } else {
                // Verification key has expired
                return 'expired';
            }
        } else {
            return false;
        }
    }

    // retrieve profile data
    public function get_profile($user_id) {
        $response = array();

        // Get profile data
        $this->db->select('email, created_at, UserName');
        $this->db->where('id', $user_id);
        $profile_query = $this->db->get('User');
        $profile_data = $profile_query->row_array();

        // Get attempt data with CategoryName
        $this->db->select('attempt.*, Category.CategoryName');
        $this->db->from('attempt');
        $this->db->join('Category', 'attempt.CategoryID = Category.CategoryID');
        $this->db->where('attempt.UserID', $user_id);
        $attempt_query = $this->db->get();
        $attempt_data = $attempt_query->result_array();

        // Get quizzes created by the user
        $this->db->select('CategoryID, CategoryName');
        $this->db->from('Category');
        $this->db->where('UserID', $user_id);
        $created_quizzes_query = $this->db->get();
        $created_quizzes_data = $created_quizzes_query->result_array();

        // Get bookmarks for the user and retrieve CategoryName for each CategoryID
        $this->db->select('Bookmark.id, Bookmark.CategoryID, Category.CategoryName');
        $this->db->from('Bookmark');
        $this->db->join('Category', 'Bookmark.CategoryID = Category.CategoryID');
        $this->db->where('Bookmark.UserID', $user_id);
        $bookmark_query = $this->db->get();
        $bookmarks_data = $bookmark_query->result_array();

        // Add profile data to the response
        $response['profile'] = $profile_data;

        // Add attempt data to the response
        $response['attempt'] = $attempt_data;

        // Add created quizzes data to the response
        $response['created_quizzes'] = $created_quizzes_data;

        // Add bookmarks data to the response
        $response['bookmarks'] = $bookmarks_data;

        return $response;
    }


    public function check_user_exists($user_id) {
        $this->db->where('id', $user_id);
        $query = $this->db->get('User');
        return $query->num_rows() > 0;
    }


    // Function to update user's password
    public function update_password($user_id, $new_password) {
        $user = $this->db->get_where('User', ['id' => $user_id])->row_array();

        if (!empty($user)) {
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $data = array(
                'password' => $hashed_password
            );
            $this->db->where('id', $user_id);
            $this->db->update('User', $data);
            return true;
        } else {
            return false;
        }
    }

    // deleting user related records
    public function delete_user($user_id) {
        $this->delete_attempts($user_id);
        $this->delete_feedbacks($user_id);
        $this->delete_bookmarks($user_id);
        $this->delete_questions($user_id);
        $this->delete_answers($user_id);
        $this->delete_tags($user_id);
        $this->delete_categories($user_id);
        $this->delete_user_record($user_id);

        return $this->db->affected_rows() > 0;
    }

    private function delete_attempts($user_id) {
        // Get the category IDs associated with the user
        $category_ids_query = $this->db->query("SELECT CategoryID FROM Category WHERE UserID = $user_id");
        $category_ids_result = $category_ids_query->result_array();
        $category_ids = array_column($category_ids_result, 'CategoryID');

        if (!empty($category_ids)) {
            // Delete attempts associated with the user's categories
            $this->db->where_in('CategoryID', $category_ids);
            $this->db->delete('attempt');
        }

        // Delete attempts where UserID matches
        $this->db->where('UserID', $user_id);
        $this->db->delete('attempt');
    }

    private function delete_feedbacks($user_id) {
        // Get the category IDs associated with the user
        $category_ids_query = $this->db->query("SELECT CategoryID FROM Category WHERE UserID = $user_id");
        $category_ids_result = $category_ids_query->result_array();
        $category_ids = array_column($category_ids_result, 'CategoryID');

        if (!empty($category_ids)) {
            // Delete attempts associated with the user's categories
            $this->db->where_in('CategoryID', $category_ids);
            $this->db->delete('feedback');
        }

        // Delete attempts where UserID matches
        $this->db->where('UserID', $user_id);
        $this->db->delete('feedback');
    }

    private function delete_bookmarks($user_id) {
        // Get the category IDs associated with the user
        $category_ids_query = $this->db->query("SELECT CategoryID FROM Category WHERE UserID = $user_id");
        $category_ids_result = $category_ids_query->result_array();
        $category_ids = array_column($category_ids_result, 'CategoryID');

        if (!empty($category_ids)) {
            // Delete attempts associated with the user's categories
            $this->db->where_in('CategoryID', $category_ids);
            $this->db->delete('bookmark');
        }

        // Delete attempts where UserID matches
        $this->db->where('UserID', $user_id);
        $this->db->delete('bookmark');
    }

    private function delete_questions($user_id) {
        $this->db->where_in('CategoryID', "SELECT CategoryID FROM Category WHERE UserID = $user_id");
        $this->db->delete('questions');
    }

    private function delete_answers($user_id) {
        $this->db->where_in('QuestionID', "SELECT QuestionID FROM questions WHERE CategoryID IN (SELECT CategoryID FROM Category WHERE UserID = $user_id)");
        $this->db->delete('answers');
    }

    private function delete_tags($user_id) {
        $this->db->where_in('CategoryID', "SELECT CategoryID FROM Category WHERE UserID = $user_id");
        $this->db->delete('tag');
    }

    private function delete_categories($user_id) {
        $this->db->where('UserID', $user_id);
        $this->db->delete('Category');
    }

    private function delete_user_record($user_id) {
        $this->db->where('id', $user_id);
        $this->db->delete('User');
    }

    public function update_user($email, $verification_key, $verification_key_expires_at) {
        $expiration_datetime = date('Y-m-d H:i:s', $verification_key_expires_at);

        // Retrieve user ID based on email
        $user = $this->db->get_where('User', ['email' => $email])->row_array();

        if (!empty($user)) {
            // Update user record with new verification key and expiration timestamp
            $data = array(
                'verification_key' => $verification_key,
                'verification_key_expires_at' => $expiration_datetime
            );

            // Perform the update
            $this->db->where('id', $user['id']); // Assuming the user ID column is 'id'
            $this->db->update('User', $data);

            // Check if the update was successful
            if ($this->db->affected_rows() > 0) {
                return true; // User record updated successfully
            } else {
                return false; // Failed to update user record
            }
        } else {
            return false; // User not found with the provided email
        }
    }

}
