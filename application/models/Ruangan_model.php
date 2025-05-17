<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Ruangan_model extends CI_Model {

    public function get_all()
    {
        return $this->db->get('ruangan')->result();
    }
    public function get_by_tipe($tipe)
    {
        return $this->db->get_where('ruangan', ['tipe_seminar' => $tipe])->result();
    }
    
    public function get_by_id($id)
    {
        return $this->db->get_where('ruangan', ['id' => $id])->row();
    }

    public function insert($data)
    {
        return $this->db->insert('ruangan', $data);
    }

    public function update($id, $data)
    {
        return $this->db->where('id', $id)->update('ruangan', $data);
    }

    public function delete($id)
    {
        return $this->db->where('id', $id)->delete('ruangan');
    }
}
