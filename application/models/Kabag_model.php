<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Kabag_model extends CI_Model {

    public function get_profile_by_id($id) {
        return $this->db->get_where('kabag', ['id' => $id])->row();
    }
}
