<?php
class PenjadwalanUjian extends CI_Controller {
    public function __construct() {
        parent::__construct();
        $this->load->model('Penjadwalan_model');
    }

    public function ajukan_ujian() {
        $tipe_ujian = $this->input->post('tipe_ujian');
        $mahasiswa_id = $this->input->post('mahasiswa_id');
        $judul_skripsi = $this->input->post('judul_skripsi');

        // Simpan pengajuan terlebih dahulu
        $pengajuan_id = $this->Penjadwalan_model->simpan_pengajuan([
            'mahasiswa_id' => $mahasiswa_id,
            'tipe_ujian' => $tipe_ujian,
            'judul_skripsi' => $judul_skripsi,
            'tanggal_pengajuan' => date('Y-m-d'),
            'status' => 'dikonfirmasi'
        ]);

        // Ambil data mahasiswa dan dosen terkait
        $mahasiswa = $this->Penjadwalan_model->get_mahasiswa($mahasiswa_id);
        $dosen1 = $mahasiswa['penguji1_id'];
        $dosen2 = $mahasiswa['penguji2_id'];
        $pembimbing = $mahasiswa['pembimbing_id'];

        // Ambil jadwal periode sesuai tipe ujian
        $periode = $this->Penjadwalan_model->get_periode($tipe_ujian);
        $tanggal_list = $this->Penjadwalan_model->generate_date_range($periode['tanggal_mulai'], $periode['tanggal_selesai']);

        // Slot waktu tetap
        $slot_waktu = ['08:45-10:25','10:30-12:10','13:00-14:40','14:45-16:25'];

        // Loop semua kombinasi tanggal dan slot untuk cari yang cocok
        foreach ($tanggal_list as $tanggal) {
            foreach ($slot_waktu as $slot) {
                $skor = $this->Penjadwalan_model->hitung_prioritas([
                    'mahasiswa_id' => $mahasiswa_id,
                    'dosen1' => $dosen1,
                    'dosen2' => $dosen2,
                    'pembimbing' => $pembimbing,
                    'tanggal' => $tanggal,
                    'slot' => $slot,
                    'tipe_ujian' => $tipe_ujian
                ]);

                if ($skor >= 0.8) { // ambang prioritas 80%
                    $ruangan = $this->Penjadwalan_model->get_ruangan_available($tipe_ujian, $tanggal, $slot);
                    if ($ruangan) {
                        // Simpan jadwal
                        $jadwal_id = $this->Penjadwalan_model->simpan_jadwal([
                            'tanggal' => $tanggal,
                            'slot' => $slot,
                            'ruangan_id' => $ruangan['id'],
                            'penguji1_id' => $dosen1,
                            'penguji2_id' => $dosen2,
                            'pembimbing_id' => $pembimbing
                        ]);

                        // Update pengajuan dengan jadwal
                        $this->Penjadwalan_model->update_jadwal_pengajuan($pengajuan_id, $jadwal_id);

                        // Simpan konfirmasi
                        $this->Penjadwalan_model->simpan_konfirmasi([
                            'pengajuan_id' => $pengajuan_id,
                            'mahasiswa_id' => $mahasiswa_id,
                            'tanggal_konfirmasi' => NULL,
                            'status_konfirmasi' => 'menunggu'
                        ]);

                        echo json_encode(['status' => 'sukses', 'jadwal_id' => $jadwal_id]);
                        return;
                    }
                }
            }
        }

        echo json_encode(['status' => 'gagal', 'pesan' => 'Tidak ada slot yang tersedia']);
    }
}
