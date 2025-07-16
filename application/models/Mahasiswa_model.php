<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Mahasiswa_model extends CI_Model {
     private $_table_mhs = "mahasiswa";
    public function get_all_mahasiswa()
    {
        return $this->db->get('mahasiswa')->result();
    }
    public function getMahasiswaById($id) {
        $this->db->select('
            mahasiswa.id, mahasiswa.nama, mahasiswa.email, mahasiswa.nim, mahasiswa.fakultas, mahasiswa.prodi, mahasiswa.tahun_masuk,
            mahasiswa.role_id, mahasiswa.is_active, mahasiswa.judul_skripsi, 
            pembimbing.nama AS pembimbing_nama,
            penguji1.nama AS penguji1_nama,
            penguji2.nama AS penguji2_nama
        ');
        $this->db->from('mahasiswa');
        $this->db->join('dosen AS pembimbing', 'mahasiswa.pembimbing_id = pembimbing.id', 'left');
        $this->db->join('dosen AS penguji1', 'mahasiswa.penguji1_id = penguji1.id', 'left');
        $this->db->join('dosen AS penguji2', 'mahasiswa.penguji2_id = penguji2.id', 'left');
        $this->db->where('mahasiswa.id', $id);
        $query = $this->db->get();
        return $query->row_array();
    }
    public function getMahasiswaBy_Id($id) {
        $this->db->select('*');
        $this->db->from('mahasiswa');
        $this->db->where('id', $id);
        $query = $this->db->get();
        return $query->row();
    }

    public function update_Mahasiswa($id, $updateData) {
        // Pastikan data pembimbing, penguji 1, dan penguji 2 diupdate berdasarkan ID dosen yang sesuai
        $this->db->where('id', $id);
        return $this->db->update('mahasiswa', $updateData);
    }
    public function getAllMahasiswaWithDetails() {
        $this->db->select('
            mahasiswa.id, 
            mahasiswa.nama, 
            mahasiswa.nim, 
            mahasiswa.email, 
            mahasiswa.tahun_masuk,
            mahasiswa.judul_skripsi, 
            dosen_pembimbing.nama AS pembimbing_nama, 
            dosen_penguji1.nama AS penguji1_nama, 
            dosen_penguji2.nama AS penguji2_nama
        ');
        $this->db->from('mahasiswa');
        $this->db->join('dosen AS dosen_pembimbing', 'dosen_pembimbing.id = mahasiswa.pembimbing_id', 'left');
        $this->db->join('dosen AS dosen_penguji1', 'dosen_penguji1.id = mahasiswa.penguji1_id', 'left');
        $this->db->join('dosen AS dosen_penguji2', 'dosen_penguji2.id = mahasiswa.penguji2_id', 'left');
        $query = $this->db->get();
        return $query->result();
    }

      public function search_mahasiswa($keyword = null) {
        // Bagian SELECT dan JOIN sama persis dengan fungsi Anda
        $this->db->select('
            mahasiswa.id, 
            mahasiswa.nama, 
            mahasiswa.nim, 
            mahasiswa.email, 
            mahasiswa.tahun_masuk,
            mahasiswa.judul_skripsi, 
            dosen_pembimbing.nama AS pembimbing_nama, 
            dosen_penguji1.nama AS penguji1_nama, 
            dosen_penguji2.nama AS penguji2_nama
        ');
        $this->db->from('mahasiswa');
        $this->db->join('dosen AS dosen_pembimbing', 'dosen_pembimbing.id = mahasiswa.pembimbing_id', 'left');
        $this->db->join('dosen AS dosen_penguji1', 'dosen_penguji1.id = mahasiswa.penguji1_id', 'left');
        $this->db->join('dosen AS dosen_penguji2', 'dosen_penguji2.id = mahasiswa.penguji2_id', 'left');

        // ---- BAGIAN LOGIKA PENCARIAN DITAMBAHKAN DI SINI ----
        if (!empty($keyword)) {
            // Mengelompokkan kondisi LIKE agar tidak bentrok dengan JOIN
            $this->db->group_start();
            $this->db->like('mahasiswa.nama', $keyword);
            $this->db->or_like('mahasiswa.nim', $keyword);
            $this->db->group_end();
        }
        // ---- AKHIR BAGIAN LOGIKA PENCARIAN ----

        // Menambahkan urutan agar hasil konsisten
        $this->db->order_by('mahasiswa.nama', 'ASC');

        $query = $this->db->get();
        return $query->result();
    }
    public function getDosenOptions() {
        $this->db->select('id, nama');
        $this->db->from('dosen');
        $query = $this->db->get();
        return $query->result();
    }

    public function updateMahasiswa($id, $data) {
        $this->db->where('id', $id);
        return $this->db->update('mahasiswa', $data);
    }
    public function get_mahasiswa_with_pembimbing() {
        $nim = $this->session->userdata('nim');
        $this->db->select('
            mahasiswa.id, 
            mahasiswa.nama, 
            mahasiswa.email, 
            mahasiswa.nim, 
            mahasiswa.fakultas, 
            mahasiswa.prodi, 
            mahasiswa.tahun_masuk,
            mahasiswa.role_id, 
            mahasiswa.is_active,  
            mahasiswa.judul_skripsi, 
            mahasiswa.pembimbing_id, 
            mahasiswa.penguji1_id, 
            mahasiswa.penguji2_id,
            mahasiswa.status_sempro,  
            mahasiswa.status_semhas,  
            dosen_pembimbing.nama AS pembimbing_nama, 
            dosen_penguji1.nama AS penguji1_nama, 
            dosen_penguji2.nama AS penguji2_nama
        ');
        $this->db->from('mahasiswa');
        // Join dengan dosen untuk pembimbing dan penguji
        $this->db->join('dosen AS dosen_pembimbing', 'dosen_pembimbing.id = mahasiswa.pembimbing_id', 'left');
        $this->db->join('dosen AS dosen_penguji1', 'dosen_penguji1.id = mahasiswa.penguji1_id', 'left');
        $this->db->join('dosen AS dosen_penguji2', 'dosen_penguji2.id = mahasiswa.penguji2_id', 'left');
        
        if ($nim) { // Pastikan NIM ada di session sebelum melakukan query
            $this->db->where('mahasiswa.nim', $nim);
        } else {
            // Handle jika NIM tidak ada di session, mungkin return array kosong atau tampilkan error
            // Untuk contoh ini, kita return array kosong jika tidak ada NIM
            return []; 
        }
        
        $query = $this->db->get();
        return $query->result(); // Mengembalikan array objek, cocok untuk foreach
    }
  
    
    
    public function get_mahasiswa_without_pembimbing_and_penguji() {
        // Ambil mahasiswa yang tidak memiliki pembimbing atau penguji
        $this->db->select('id, nama, email, nim, fakultas, prodi, role_id, is_active, judul_skripsi,pembimbing_id, penguji1_id, penguji2_id');
        $this->db->from('mahasiswa');
        // Mengambil mahasiswa yang belum memiliki pembimbing (pembimbing_id NULL) atau belum memiliki penguji
        $this->db->where('(pembimbing_id IS NULL OR penguji1_id IS NULL OR penguji2_id IS NULL)');
        $query = $this->db->get();
        return $query->result();
    }
    
    
    public function assign_pembimbing_dan_penguji($mahasiswa_id, $dosen_pembimbing_id, $dosen_penguji1_id, $dosen_penguji2_id) {
        // Update pembimbing_id dan penguji_id mahasiswa
        $data = array(
            'pembimbing_id' => $dosen_pembimbing_id,
            'penguji1_id' => $dosen_penguji1_id,
            'penguji2_id' => $dosen_penguji2_id
        );
        
        // Melakukan update data mahasiswa berdasarkan ID
        $this->db->where('id', $mahasiswa_id);
        return $this->db->update('mahasiswa', $data); // Mengembalikan hasil update (true/false)
    }

      public function get_mahasiswa_by_pembimbing($pembimbing_id) {
        $this->db->select('mahasiswa.id, mahasiswa.nama, mahasiswa.nim, mahasiswa.judul_skripsi, mahasiswa.prodi,mahasiswa.tahun_masuk, mahasiswa.fakultas, mahasiswa.status_sempro, mahasiswa.status_semhas, dosen_pembimbing.nama AS pembimbing_nama, 
            dosen_penguji1.nama AS penguji1_nama, 
            dosen_penguji2.nama AS penguji2_nama');
        $this->db->from('mahasiswa');
         $this->db->join('dosen AS dosen_pembimbing', 'dosen_pembimbing.id = mahasiswa.pembimbing_id', 'left');
        $this->db->join('dosen AS dosen_penguji1', 'dosen_penguji1.id = mahasiswa.penguji1_id', 'left');
        $this->db->join('dosen AS dosen_penguji2', 'dosen_penguji2.id = mahasiswa.penguji2_id', 'left');
        $this->db->where('pembimbing_id', $pembimbing_id);
        $query = $this->db->get();
        return $query->result();
    }
    public function get_all_mahasiswa_with_details() {
        $this->db->select('
            mahasiswa.id, 
            mahasiswa.nama, 
            mahasiswa.nim, 
            mahasiswa.email, 
            mahasiswa.tahun_masuk,
            mahasiswa.judul_skripsi, 
            dosen_pembimbing.nama AS pembimbing_nama, 
            dosen_penguji1.nama AS penguji1_nama, 
            dosen_penguji2.nama AS penguji2_nama
        ');
        $this->db->from('mahasiswa');
        $this->db->join('dosen AS dosen_pembimbing', 'dosen_pembimbing.id = mahasiswa.pembimbing_id', 'left');
        $this->db->join('dosen AS dosen_penguji1', 'dosen_penguji1.id = mahasiswa.penguji1_id', 'left');
        $this->db->join('dosen AS dosen_penguji2', 'dosen_penguji2.id = mahasiswa.penguji2_id', 'left');
        $query = $this->db->get();
        return $query->result();
    }
    public function get_mahasiswa_seminar_status() {
    $this->db->select('id, nama, nim, status_sempro, status_semhas');
    $this->db->from('mahasiswa');
    // You might want to filter by role_id if only students should appear
    // $this->db->where('role_id', 3); // Assuming role_id 3 is for students
    $this->db->order_by('nama', 'ASC'); // Optional: order by name
    $query = $this->db->get();
    return $query->result();
}
    public function delete_mahasiswa($id) {
        return $this->db->delete('mahasiswa', ['id' => $id]);
    }
  public function update_status_mahasiswa($mahasiswa_id, $data)
    {
        $this->db->where('id', $mahasiswa_id);
        return $this->db->update('mahasiswa', $data);
    }
 public function get_mahasiswa_by_id($mahasiswa_id) {
        $this->db->where('id', $mahasiswa_id);
        $query = $this->db->get('mahasiswa');
        return $query->row(); // Mengembalikan satu baris data mahasiswa
    }


}


