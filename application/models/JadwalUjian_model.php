<?php
class JadwalUjian_model extends CI_Model {

    public function generateJadwalRekomendasi($pengajuan_id) {
        // Load the Pengajuan_model
        $this->load->model('Pengajuan_model');

        // Get the pengajuan data
        $pengajuan = $this->Pengajuan_model->get_pengajuan_by_id($pengajuan_id);

        // Verifikasi keberadaan data
        if (!isset($pengajuan->pembimbing_id) || 
            !isset($pengajuan->penguji1_id) || 
            !isset($pengajuan->penguji2_id)) {
            return ['error' => 'Data pengajuan tidak lengkap'];
        }

        $pembimbing_id = $pengajuan->pembimbing_id;
        $penguji1_id = $pengajuan->penguji1_id;
        $penguji2_id = $pengajuan->penguji2_id;

        // Inisialisasi slot waktu (contoh sederhana)
        $slot_waktu = [];
        for ($hari = 1; $hari <= 5; $hari++) {
            for ($jam = 8; $jam <= 17; $jam++) {
                $slot_waktu[$hari][$jam] = true; // Awalnya semua slot kosong
            }
        }

        $jadwal_rekomendasi = [];
        foreach ($slot_waktu as $hari => $jam_hari) {
            foreach ($jam_hari as $jam => $status) {
                if ($status) {
                    // Buat objek jadwal
                    $jadwal = [
                        'pengajuan_id' => $pengajuan_id,
                        'tanggal' => $hari,
                        'waktu_mulai' => $jam,
                        'waktu_selesai' => $jam + 1,
                        'status' => 'Tersedia',
                    ];
                    $jadwal_rekomendasi[] = $jadwal;
                }
            }
        }

        return $jadwal_rekomendasi;
    }

    public function simpanJadwal($jadwal) {
        // Pastikan data jadwal hanya berisi kolom yang ada di tabel
        $data = [
            'pengajuan_id' => $jadwal['id_pengajuan'], // Ubah 'id_pengajuan' menjadi 'pengajuan_id'
            'tanggal' => $jadwal['tanggal'],
            'waktu_mulai' => $jadwal['waktu_mulai'],
            'waktu_selesai' => $jadwal['waktu_selesai'],
            'status' => 'Tersedia' // Atau status lainnya sesuai kebutuhan
        ];
        
        // Cek apakah semua kunci ada dalam array
        if (isset($jadwal['id_pengajuan'], $jadwal['tanggal'], $jadwal['waktu_mulai'], $jadwal['waktu_selesai'])) {
            $this->db->insert('jadwal_ujian', $data);
            return $this->db->insert_id();
        } else {
            return ['error' => 'Data jadwal tidak lengkap'];
        }
    }
    
    private function getLecturerSchedule($lecturer_id) {
        // Ambil data jadwal dari database berdasarkan ID dosen
        $query = $this->db->get_where('lecturer_schedule', array('lecturer_id' => $lecturer_id));
        return $query->result_array(); // Pastikan ini mengembalikan array
    }

    private function findAvailableSlot($pembimbing_schedule, $penguji1_schedule, $penguji2_schedule) {
        // Periksa apakah array jadwal tidak kosong
        if (empty($pembimbing_schedule) || empty($penguji1_schedule) || empty($penguji2_schedule)) {
            return null; // Kembalikan null jika jadwal kosong
        }

        // Logika untuk menemukan slot waktu yang tersedia
        // Contoh placeholder, sesuaikan dengan kebutuhan
        return ['slot_time' => '2024-12-12 10:00:00', 'room' => 'A101'];
    }
    public function getJadwalByMahasiswa($mahasiswa_id) {
        $this->db->select('*');
        $this->db->from('jadwal_ujian');
        $this->db->join('pengajuan_ujian', 'jadwal_ujian.pengajuan_id = pengajuan_ujian.id');
        $this->db->where('pengajuan_ujian.mahasiswa_id', $mahasiswa_id);
        $query = $this->db->get();
        return $query->result_array(); // Mengembalikan data dalam bentuk array
    }

    // Metode untuk mendapatkan jadwal ujian berdasarkan ID dosen
    public function getJadwalByDosen($dosenId) {
        $this->db->select('jadwal_ujian.*, pengajuan_ujian.judul_skripsi, mahasiswa.nama AS mahasiswa_nama');
        $this->db->from('jadwal_ujian');
        $this->db->join('pengajuan_ujian', 'jadwal_ujian.pengajuan_id = pengajuan_ujian.id');
        $this->db->join('mahasiswa', 'pengajuan_ujian.mahasiswa_id = mahasiswa.id');
        $this->db->where('mahasiswa.pembimbing_id', $dosenId);
        $this->db->or_where('mahasiswa.penguji1_id', $dosenId);
        $this->db->or_where('mahasiswa.penguji2_id', $dosenId);
        $query = $this->db->get();
        return $query->result_array();
    }
}
?>