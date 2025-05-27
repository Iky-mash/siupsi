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
        if (isset($_FILES["file"]["name"]) && $_FILES["file"]["error"] == UPLOAD_ERR_OK) {
            $path = $_FILES["file"]["tmp_name"];
            
            try {
                $spreadsheet = IOFactory::load($path);
            } catch (\PhpOffice\PhpSpreadsheet\Reader\Exception $e) {
                $this->session->set_flashdata('error', 'Gagal membaca file Excel. Pastikan format file benar. Error: ' . $e->getMessage());
                redirect('excel_import');
                return;
            }

            $worksheet = $spreadsheet->getActiveSheet();
            $highestRow = $worksheet->getHighestRow();
    
            $existingEmailsInExcel = [];
            $existingNimsInExcel = [];
            $duplicateEmailsInExcel = [];
            $duplicateNimsInExcel = [];
    
            // Cek data duplikat dalam Excel
            for ($row = 2; $row <= $highestRow; $row++) {
                $emailCell = 'B' . $row;
                $nimCell = 'D' . $row;

                if ($worksheet->cellExists($emailCell)) {
                    // FIX: Cast to string before trim
                    $email = strtolower(trim((string) $worksheet->getCell($emailCell)->getValue())); 
                    if (!empty($email)) { 
                        if (in_array($email, $existingEmailsInExcel)) {
                            $duplicateEmailsInExcel[] = $email . " (baris " . $row . ")";
                        } else {
                            $existingEmailsInExcel[] = $email;
                        }
                    }
                }

                if ($worksheet->cellExists($nimCell)) {
                    // FIX: Cast to string before trim
                    $nim = trim((string) $worksheet->getCell($nimCell)->getValue()); 
                    if (!empty($nim)) { 
                        if (in_array($nim, $existingNimsInExcel)) {
                            $duplicateNimsInExcel[] = $nim . " (baris " . $row . ")";
                        } else {
                            $existingNimsInExcel[] = $nim;
                        }
                    }
                }
            }
    
            if (!empty($duplicateEmailsInExcel) || !empty($duplicateNimsInExcel)) {
                $errorMessage = 'Ditemukan data duplikat di dalam file Excel: ';
                if (!empty($duplicateEmailsInExcel)) {
                    $errorMessage .= 'Email duplikat: ' . implode(', ', array_unique($duplicateEmailsInExcel)) . '. ';
                }
                if (!empty($duplicateNimsInExcel)) {
                    $errorMessage .= 'NIM duplikat: ' . implode(', ', array_unique($duplicateNimsInExcel)) . '.';
                }
                $this->session->set_flashdata('error', $errorMessage);
                redirect('excel_import'); 
                return; 
            }
    
            $importedCount = 0;
            $skippedCount = 0;
            $errors = [];

            for ($row = 2; $row <= $highestRow; $row++) {
                // FIX: Cast to string before trim for all relevant fields
                $nama = trim((string) $worksheet->getCell('A' . $row)->getValue());
                $email = strtolower(trim((string) $worksheet->getCell('B' . $row)->getValue()));
                $password_val = (string) $worksheet->getCell('C' . $row)->getValue(); // Cast to string, no trim needed before hash
                $nim = trim((string) $worksheet->getCell('D' . $row)->getValue());
                
                $pembimbing_id_val = trim((string) $worksheet->getCell('E' . $row)->getValue()); // Line 113 in original
                $penguji1_id_val = trim((string) $worksheet->getCell('F' . $row)->getValue());   // Line 114 in original
                $penguji2_id_val = trim((string) $worksheet->getCell('G' . $row)->getValue());   // Line 115 in original

                if (empty($nama) && empty($email) && empty($nim) && empty($password_val)) {
                    // Baris ini kemungkinan kosong, lewati saja tanpa pesan error spesifik per baris
                    // Ini untuk menghindari pesan error jika ada banyak baris kosong di akhir file
                    continue;
                }

                if (empty($nama) || empty($email) || empty($nim) || empty($password_val)) {
                    $skippedCount++;
                    $errors[] = "Data pada baris {$row} tidak lengkap (Nama, Email, Password, atau NIM kosong) dan dilewati.";
                    continue; 
                }
    
                // Validasi apakah ID Dosen ada (Opsional tapi direkomendasikan)
                if (!empty($pembimbing_id_val) && !$this->db->where('id', $pembimbing_id_val)->get('dosen')->row()) {
                    $skippedCount++;
                    $errors[] = "Pembimbing ID '{$pembimbing_id_val}' pada baris {$row} tidak ditemukan di database dosen dan dilewati.";
                    continue;
                }
                if (!empty($penguji1_id_val) && !$this->db->where('id', $penguji1_id_val)->get('dosen')->row()) {
                    $skippedCount++;
                    $errors[] = "Penguji 1 ID '{$penguji1_id_val}' pada baris {$row} tidak ditemukan di database dosen dan dilewati.";
                    continue;
                }
                if (!empty($penguji2_id_val) && !$this->db->where('id', $penguji2_id_val)->get('dosen')->row()) {
                    $skippedCount++;
                    $errors[] = "Penguji 2 ID '{$penguji2_id_val}' pada baris {$row} tidak ditemukan di database dosen dan dilewati.";
                    continue;
                }


                $this->db->group_start(); // Penting untuk query OR yang benar
                $this->db->where('email', $email);
                $this->db->or_where('nim', $nim);
                $this->db->group_end();
                $cekDatabase = $this->db->get('mahasiswa')->row();
    
                if (!$cekDatabase) {
                    $data = array(
                        'nama' => $nama,
                        'email' => $email,
                        'password' => password_hash($password_val, PASSWORD_DEFAULT),
                        'nim' => $nim,
                        'pembimbing_id' => !empty($pembimbing_id_val) ? (int)$pembimbing_id_val : NULL,
                        'penguji1_id' => !empty($penguji1_id_val) ? (int)$penguji1_id_val : NULL,
                        'penguji2_id' => !empty($penguji2_id_val) ? (int)$penguji2_id_val : NULL,
                        'role_id' => 3,
                        'is_active' => 1 
                    );
    
                    if ($this->db->insert('mahasiswa', $data)) {
                        $importedCount++;
                    } else {
                        $skippedCount++;
                        $errors[] = "Gagal menyimpan data untuk NIM {$nim} / Email {$email} ke database (Error DB: ".$this->db->error()['message'].").";
                    }
                } else {
                    $skippedCount++;
                    $reason = "";
                    if (strtolower($cekDatabase->email) == strtolower($email)) $reason .= "Email '{$email}' sudah ada. ";
                    if ($cekDatabase->nim == $nim) $reason .= "NIM '{$nim}' sudah ada.";
                    $errors[] = "Data pada baris {$row} dilewati: {$reason}";
                }
            }
    
            $flashSuccess = "";
            $flashError = "";

            if ($importedCount > 0) {
                $flashSuccess .= "{$importedCount} data mahasiswa berhasil diimport. ";
            }
            
            if ($skippedCount > 0) {
                $flashError .= "{$skippedCount} data dilewati.";
                if(!empty($errors)){
                     $flashError .= " Rincian: " . implode(" | ", $errors);
                }
            } else if ($importedCount == 0 && $highestRow < 2) { // Hanya header atau file kosong
                 $flashError .= 'Tidak ada data untuk diimport dalam file Excel (file kosong atau hanya header).';
            } else if ($importedCount == 0 && $skippedCount == 0 && $highestRow >=2) { // Tidak ada data baru dan tidak ada yg diskip (semua sudah ada)
                $flashError .= 'Tidak ada data baru yang diimport (kemungkinan semua data sudah ada atau format tidak sesuai).';
            }


            if(!empty($flashSuccess)){
                $this->session->set_flashdata('success', $flashSuccess);
            }
            if(!empty($flashError)){
                $this->session->set_flashdata('error', $flashError);
            }
    
            redirect('admin/data_mahasiswa');
        } else {
            $errorMessage = 'Tidak ada file yang diupload atau terjadi kesalahan saat upload.';
            if(isset($_FILES["file"]["error"]) && $_FILES["file"]["error"] != UPLOAD_ERR_OK) {
                switch ($_FILES["file"]["error"]) {
                    case UPLOAD_ERR_INI_SIZE:
                    case UPLOAD_ERR_FORM_SIZE:
                        $errorMessage .= " Ukuran file melebihi batas.";
                        break;
                    case UPLOAD_ERR_PARTIAL:
                        $errorMessage .= " File hanya terupload sebagian.";
                        break;
                    case UPLOAD_ERR_NO_FILE:
                        $errorMessage .= " Tidak ada file yang diupload.";
                        break;
                    default:
                        $errorMessage .= " Error upload tidak diketahui.";
                        break;
                }
            }
            $this->session->set_flashdata('error', $errorMessage);
            redirect('excel_import'); 
        }
    }
    
    
}
?>
