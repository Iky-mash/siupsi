<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class JadwalUjian extends CI_Controller {
    public function __construct() {
        parent::__construct();
        $this->load->model('Agenda_model');
    }

    public function buatJadwalOtomatis($pengajuanId) {
        // Ambil data pengajuan ujian
        $pengajuan = $this->db->get_where('pengajuan_ujian', ['id' => $pengajuanId])->row_array();

        if (!$pengajuan) {
            show_error('Pengajuan tidak ditemukan');
        }

        // Ambil ID dosen
        $dosenIds = [$pengajuan['pembimbing_id'], $pengajuan['penguji1_id'], $pengajuan['penguji2_id']];

        // Ambil agenda dosen selama satu bulan ke depan
        $startDate = date('Y-m-d');
        $endDate = date('Y-m-d', strtotime('+1 month'));
        $agendaDosen = $this->Agenda_model->getAgendaDosen($dosenIds, $startDate, $endDate);

        // Buat slot waktu kerja
        $slotKerja = $this->generateSlotKerja();

        // Cari slot kosong untuk masing-masing dosen
        $kosongPembimbing = $this->hitungSlotKosong($slotKerja, $agendaDosen, $pengajuan['pembimbing_id']);
        $kosongPenguji1 = $this->hitungSlotKosong($slotKerja, $agendaDosen, $pengajuan['penguji1_id']);
        $kosongPenguji2 = $this->hitungSlotKosong($slotKerja, $agendaDosen, $pengajuan['penguji2_id']);

        // Cari irisan slot waktu
        $slotRekomendasi = $this->cariIrisanSlot($kosongPembimbing, $kosongPenguji1, $kosongPenguji2);

        if (empty($slotRekomendasi)) {
            show_error('Tidak ditemukan jadwal yang cocok');
        }

        // Simpan jadwal ujian
        $jadwalData = [
            'pengajuan_id' => $pengajuanId,
            'tanggal' => $slotRekomendasi['tanggal'],
            'waktu_mulai' => $slotRekomendasi['waktu_mulai'],
            'waktu_selesai' => $slotRekomendasi['waktu_selesai'],
            'status' => 'Terjadwal'
        ];
        $jadwalId = $this->Agenda_model->saveJadwalUjian($jadwalData);

        // Berikan notifikasi kepada dosen
        // (implementasi notifikasi di sini)

        // Redirect ke halaman detail pengajuan
        redirect('pengajuan/detail/' . $pengajuanId);
    }

    private function generateSlotKerja() {
        $hariKerja = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'];
        $jamKerja = [
            ['08:00', '12:00'],
            ['13:00', '16:30']
        ];

        $slots = [];
        for ($i = 0; $i < 30; $i++) {
            $tanggal = date('Y-m-d', strtotime("+$i days"));
            if (in_array(date('l', strtotime($tanggal)), $hariKerja)) {
                foreach ($jamKerja as $jam) {
                    $slots[] = [
                        'tanggal' => $tanggal,
                        'waktu_mulai' => $jam[0],
                        'waktu_selesai' => $jam[1]
                    ];
                }
            }
        }
        return $slots;
    }

    private function hitungSlotKosong($slotKerja, $agendaDosen, $idDosen) {
        $kosong = [];
        foreach ($slotKerja as $slot) {
            $isOccupied = false;
            foreach ($agendaDosen as $agenda) {
                if ($agenda['id_dosen'] == $idDosen &&
                    $agenda['tanggal'] == $slot['tanggal'] &&
                    $agenda['waktu_mulai'] < $slot['waktu_selesai'] &&
                    $agenda['waktu_selesai'] > $slot['waktu_mulai']) {
                    $isOccupied = true;
                    break;
                }
            }
            if (!$isOccupied) {
                $kosong[] = $slot;
            }
        }
        return $kosong;
    }

    private function cariIrisanSlot($kosong1, $kosong2, $kosong3) {
        foreach ($kosong1 as $slot1) {
            foreach ($kosong2 as $slot2) {
                if ($slot1 == $slot2) {
                    foreach ($kosong3 as $slot3) {
                        if ($slot1 == $slot3) {
                            return $slot1;
                        }
                    }
                }
            }
        }
        return null;
    }
}
