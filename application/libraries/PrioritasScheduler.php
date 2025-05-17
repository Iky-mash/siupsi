<?php
class PrioritasScheduler
{
    protected $CI;

    public function __construct()
    {
        $this->CI =& get_instance();
        $this->CI->load->model('Pengajuan_model');
        $this->CI->load->model('Jadwal_model');
        $this->CI->load->model('Dosen_model');
    }

    public function jadwalkan()
    {
        // 1. Ambil semua pengajuan yang sudah dikonfirmasi tapi belum dijadwalkan
        $pengajuanList = $this->CI->Pengajuan_model->getPengajuanSiapJadwal();

        // 2. Hitung skor prioritas
        foreach ($pengajuanList as &$pengajuan) {
            $pengajuan->skor_prioritas = $this->hitungPrioritas($pengajuan);
        }

        // 3. Urutkan dari skor prioritas tertinggi
        usort($pengajuanList, function($a, $b) {
            return $a->skor_prioritas <=> $b->skor_prioritas;
        });

        // 4. Proses penjadwalan
        foreach ($pengajuanList as $pengajuan) {
            $jadwal = $this->cariSlotUjian($pengajuan);

            if ($jadwal) {
                $this->CI->Jadwal_model->insertJadwalUjian($jadwal);
                $this->CI->Pengajuan_model->updateStatusTerjadwal($pengajuan->id, $jadwal['id']);
            }
        }
    }

    private function hitungPrioritas($pengajuan)
    {
        $selisih = (strtotime($pengajuan->tanggal_konfirmasi) - strtotime($pengajuan->tanggal_pengajuan)) / 86400; // hari
        return max(1, 10 - $selisih); // Skor max 10, makin lama makin kecil
    }

    private function cariSlotUjian($pengajuan)
    {
        $periode = $this->CI->Jadwal_model->getPeriode($pengajuan->tipe_ujian);
        $agendaPembimbing = $this->CI->Dosen_model->getAgendaByDosen($pengajuan->pembimbing_id, $periode);
        $agendaPenguji1 = $this->CI->Dosen_model->getAgendaByDosen($pengajuan->penguji1_id, $periode);
        $agendaPenguji2 = $this->CI->Dosen_model->getAgendaByDosen($pengajuan->penguji2_id, $periode);
        $ruanganTersedia = $this->CI->Jadwal_model->getRuanganAvailable($pengajuan->tipe_ujian);

        foreach ($periode as $tanggal) {
            foreach ($agendaPembimbing as $slot1) {
                foreach ($agendaPenguji1 as $slot2) {
                    foreach ($agendaPenguji2 as $slot3) {
                        if ($slot1['tanggal'] == $tanggal && $slot2['tanggal'] == $tanggal && $slot3['tanggal'] == $tanggal) {
                            $commonSlot = $this->cariSlotBersama($slot1['slot_waktu'], $slot2['slot_waktu'], $slot3['slot_waktu']);
                            if ($commonSlot) {
                                foreach ($ruanganTersedia as $ruang) {
                                    return [
                                        'mahasiswa_id' => $pengajuan->mahasiswa_id,
                                        'judul_skripsi' => $pengajuan->judul_skripsi,
                                        'tipe_ujian' => strtolower($pengajuan->tipe_ujian),
                                        'tanggal' => $tanggal,
                                        'slot_waktu' => $commonSlot,
                                        'ruangan_id' => $ruang['id'],
                                        'pembimbing_id' => $pengajuan->pembimbing_id,
                                        'penguji1_id' => $pengajuan->penguji1_id,
                                        'penguji2_id' => $pengajuan->penguji2_id,
                                    ];
                                }
                            }
                        }
                    }
                }
            }
        }
        return false; // Tidak ditemukan slot
    }

    private function cariSlotBersama($slot1, $slot2, $slot3)
    {
        $s1 = explode(',', $slot1);
        $s2 = explode(',', $slot2);
        $s3 = explode(',', $slot3);

        $common = array_intersect($s1, $s2, $s3);
        return count($common) ? array_values($common)[0] : false;
    }
}
