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
     public function get_available_rooms_by_type_and_schedule($tipe_ujian, $tanggal, $slot_waktu, $exclude_ruangan_id = null) {
        // Subquery to find ruangan_id that are already booked for the given date and time
        // and are not 'Ditolak' (since a Ditolak schedule doesn't occupy the room)
        $this->db->select('ruangan_id');
        $this->db->from('jadwal_ujian');
        $this->db->where('tanggal', $tanggal);
        $this->db->where('slot_waktu', $slot_waktu);
        $this->db->where('status_konfirmasi !=', 'Ditolak'); // Only consider confirmed or pending as booked
        $booked_rooms_subquery = $this->db->get_compiled_select();

        // Main query to find rooms
        $this->db->select('id, nama_ruangan, kapasitas, tipe_seminar');
        $this->db->from('ruangan');
        $this->db->where('LOWER(tipe_seminar)', strtolower($tipe_ujian));
        
        // Exclude rooms that are in the subquery result (i.e., already booked)
        $this->db->where("id NOT IN ($booked_rooms_subquery)", NULL, FALSE);

        if ($exclude_ruangan_id) {
            $this->db->where('id !=', $exclude_ruangan_id);
        }
        $this->db->order_by('nama_ruangan', 'ASC');
        
        $query = $this->db->get();
        return $query->result();
    }
}
