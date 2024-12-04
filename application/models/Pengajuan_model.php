<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pengajuan_model extends CI_Model {

    public function insert($data) {
        $this->db->insert('pengajuan_ujian', $data);
    }

    public function get_all() {
        return $this->db->get('pengajuan_ujian')->result_array();
    }

    public function get_by_mahasiswa($mahasiswa_id) {
        return $this->db->get_where('pengajuan_ujian', ['mahasiswa_id' => $mahasiswa_id])->result_array();
    }

    public function update_status($id, $status, $alasan_penolakan = null) {
        $data = ['status' => $status];
        if ($status == 'Ditolak') {
            $data['alasan_penolakan'] = $alasan_penolakan;
        }
        $this->db->where('id', $id);
        $this->db->update('pengajuan_ujian', $data);
    }
    public function get_pengajuan_ujian() {
        $this->db->select('pengajuan_ujian.*, mahasiswa.nama AS nama_mahasiswa, mahasiswa.nim');
        $this->db->from('pengajuan_ujian');
        $this->db->join('mahasiswa', 'mahasiswa.id = pengajuan_ujian.mahasiswa_id');
        $this->db->where('pengajuan_ujian.status', 'Diajukan');
        $query = $this->db->get();
        return $query->result_array();
    }

    // Mendapatkan pengajuan ujian berdasarkan ID
    public function get_pengajuan_by_id($id) {
        $this->db->select('*');
        $this->db->from('pengajuan_ujian');
        $this->db->where('id', $id);
        $query = $this->db->get();
        return $query->row_array();
    }
    public function get_pengajuan_by_mahasiswa($mahasiswa_id) {
        $this->db->where('mahasiswa_id', $mahasiswa_id);
        $query = $this->db->get('pengajuan_ujian');
        return $query->result_array();
    }
    // Mengecek apakah mahasiswa sudah melakukan seminar proposal
    public function cek_status_pengajuan($mahasiswa_id) {
        $this->db->select('status');
        $this->db->from('pengajuan_ujian');
        $this->db->where('mahasiswa_id', $mahasiswa_id);
        $this->db->where('status', 'Diajukan');
        $query = $this->db->get();
        return $query->num_rows() > 0;
    }

    // Mengupdate status pengajuan ujian
    public function update_status_pengajuan($id, $status, $alasan = '') {
        $data = array(
            'status' => $status,
            'alasan_penolakan' => $alasan,
            'updated_at' => date('Y-m-d H:i:s')
        );
        $this->db->where('id', $id);
        return $this->db->update('pengajuan_ujian', $data);
    }
}
