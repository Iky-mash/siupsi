<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require 'vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\IOFactory;

class Excel_import extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->database();
        $this->load->model('Excel_model');
    }

    public function index() {
        $this->load->view('admin/data_mahasiswa');
    }

public function import()
{
    // 1. Cek Upload File
    if (!isset($_FILES["file"]["name"]) || $_FILES["file"]["error"] != UPLOAD_ERR_OK) {
        $this->session->set_flashdata('error', 'Gagal meng-upload file. Pastikan Anda sudah memilih file Excel yang benar.');
        redirect('admin/data_mahasiswa');
        return;
    }

    // 2. Muat File Excel
    try {
        $path = $_FILES["file"]["tmp_name"];
        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($path);
        $worksheet = $spreadsheet->getActiveSheet();
        $highestRow = $worksheet->getHighestDataRow(); 
    } catch (\Exception $e) {
        $this->session->set_flashdata('error', 'Gagal membaca file Excel. Error: ' . $e->getMessage());
        redirect('admin/data_mahasiswa');
        return;
    }
    
    if ($highestRow < 2) {
        $this->session->set_flashdata('error', 'File Excel kosong atau hanya berisi header.');
        redirect('admin/data_mahasiswa');
        return;
    }

    $importedCount = 0;
    $skippedCount = 0;
    $errors = [];

    // 3. Mulai Perulangan untuk Membaca Setiap Baris
    for ($row = 2; $row <= $highestRow; $row++) {
        
        $nama = trim((string) $worksheet->getCell('A' . $row)->getValue());
        $email = trim((string) $worksheet->getCell('B' . $row)->getValue());
        $password_val = trim((string) $worksheet->getCell('C' . $row)->getValue());
        $nim = trim((string) $worksheet->getCell('D' . $row)->getValue());
        $fakultas = trim((string) $worksheet->getCell('E' . $row)->getValue());
        $prodi = trim((string) $worksheet->getCell('F' . $row)->getValue());
        $tahun_masuk = trim((string) $worksheet->getCell('G' . $row)->getValue());
        
        // --- KODE YANG HILANG, SEKARANG DITAMBAHKAN KEMBALI ---
        $pembimbing_id_val = trim((string) $worksheet->getCell('H' . $row)->getValue());
        $penguji1_id_val = trim((string) $worksheet->getCell('I' . $row)->getValue());
        $penguji2_id_val = trim((string) $worksheet->getCell('J' . $row)->getValue());

        // Validasi #1: Jika baris utama kosong, anggap sudah akhir dari data dan berhenti.
        if (empty($nama) && empty($email) && empty($nim)) {
            break; 
        }

        // Validasi #2: Cek data wajib
        $dataWajib = ['Nama' => $nama, 'Email' => $email, 'Password' => $password_val, 'NIM' => $nim, 'Tahun Masuk' => $tahun_masuk];
        $kolomKosong = [];
        foreach ($dataWajib as $key => $value) {
            if (empty($value)) {
                $kolomKosong[] = $key;
            }
        }
        if (!empty($kolomKosong)) {
            $errors[] = "Baris {$row} dilewati: Kolom (" . implode(', ', $kolomKosong) . ") tidak boleh kosong.";
            $skippedCount++;
            continue;
        }
        
        // Sisa validasi tetap sama...
        if (!is_numeric($tahun_masuk) || strlen($tahun_masuk) != 4) {
            $errors[] = "Baris {$row} dilewati: Format Tahun Masuk '{$tahun_masuk}' tidak valid.";
            $skippedCount++;
            continue;
        }
        
        $this->db->group_start()->where('email', $email)->or_where('nim', $nim)->group_end();
        if ($this->db->get('mahasiswa')->num_rows() > 0) {
            $errors[] = "Baris {$row} dilewati: Email atau NIM '{$nim}' sudah ada.";
            $skippedCount++;
            continue;
        }

        // --- PENYIMPANAN DATA ID DOSEN DITAMBAHKAN KEMBALI ---
        $data = [
            'nama' => $nama,
            'email' => $email,
            'password' => password_hash($password_val, PASSWORD_DEFAULT),
            'nim' => $nim,
            'fakultas' => $fakultas,
            'prodi' => $prodi,
            'tahun_masuk' => (int)$tahun_masuk,
            'pembimbing_id' => !empty($pembimbing_id_val) ? (int)$pembimbing_id_val : NULL,
            'penguji1_id' => !empty($penguji1_id_val) ? (int)$penguji1_id_val : NULL,
            'penguji2_id' => !empty($penguji2_id_val) ? (int)$penguji2_id_val : NULL,
            'role_id' => 3,
            'is_active' => 1
        ];

        // Masukkan ke database
        if ($this->db->insert('mahasiswa', $data)) {
            $importedCount++;
        } else {
            $errors[] = "Baris {$row} gagal disimpan karena error database.";
            $skippedCount++;
        }
    }

    // 4. Siapkan Pesan Hasil (tetap sama)
    if ($importedCount > 0) {
        $this->session->set_flashdata('success', "{$importedCount} data mahasiswa berhasil diimpor.");
    }

    if ($skippedCount > 0) {
        $existing_error = $this->session->flashdata('error');
        $error_message = ($existing_error ? $existing_error . "<br>" : "") . "{$skippedCount} data gagal atau dilewati.<br>Rincian:<br><ul>";
        foreach($errors as $err) {
            $error_message .= "<li>{$err}</li>";
        }
        $error_message .= "</ul>";
        $this->session->set_flashdata('error', $error_message);
    }
    
    redirect('admin/data_mahasiswa');
}
}
?>