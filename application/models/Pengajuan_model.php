<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pengajuan_model extends CI_Model {

    // Fungsi untuk menyimpan data pengajuan
    public function insert($data) {
        return $this->db->insert('pengajuan_ujian_prioritas', $data);
    }

    // Fungsi untuk mendapatkan pengajuan berdasarkan mahasiswa_id
  
    public function get_pengajuan_by_mahasiswa($mahasiswa_id) {
        return $this->db->get_where('pengajuan_ujian_prioritas', ['mahasiswa_id' => $mahasiswa_id])->result();
    }
    
    public function insert_konfirmasi($data) {
        return $this->db->insert('konfirmasi_pengajuan', $data);
    }
    public function get_all_pengajuan()
    {
        $this->db->select('pengajuan_ujian_prioritas.*, mahasiswa.nama, mahasiswa.nim, mahasiswa.fakultas, mahasiswa.prodi');
        $this->db->from('pengajuan_ujian_prioritas');
        $this->db->join('mahasiswa', 'pengajuan_ujian_prioritas.mahasiswa_id = mahasiswa.id');
        return $this->db->get()->result_array();
    }
    public function cek_pengajuan_aktif($mahasiswa_id, $tipe_ujian)
{
    $this->db->where('mahasiswa_id', $mahasiswa_id);
    $this->db->where('tipe_ujian', $tipe_ujian);
    $this->db->where('status', 'draft');
    return $this->db->get('pengajuan_ujian_prioritas')->row();
}
public function get_pengajuan_by_id($id) {
    return $this->db->get_where('pengajuan_ujian_prioritas', ['id' => $id])->row_array();
}

}
