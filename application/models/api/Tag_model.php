<?php

class Tag_model extends CI_Model{

    public function __construct(){
        parent::__construct();
    }

    public function insert_tag($data = array()){
        $this->db->insert("Tag", $data);
        return $this->db->insert_id(); // This returns the last inserted ID
    }
}

?>
