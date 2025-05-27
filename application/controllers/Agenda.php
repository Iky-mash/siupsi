<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// Tambahkan ini di awal jika belum ada (untuk PhpSpreadsheet)
require FCPATH . 'vendor/autoload.php'; // Sesuaikan path jika perlu
use PhpOffice\PhpSpreadsheet\IOFactory;

class Agenda extends CI_Controller {
    public function __construct() {
        parent::__construct();
        $this->load->model('Agenda_model');
        $this->load->model('JadwalPeriode_model'); // Jika masih digunakan
        $this->load->library('form_validation');
        $this->load->helper('url');
        $this->load->library('session');
        $this->load->library('upload'); // Ditambahkan untuk process_import_excel
    }

    public function index() {
        $data['title'] = 'Agenda Saya';
        $id_dosen = $this->session->userdata('id_dosen');

        if (!$id_dosen) {
            redirect('auth');
        }

        $all_agenda = $this->Agenda_model->get_all_agenda_raw();
        
        $filtered_agenda = array_filter($all_agenda, function ($a) use ($id_dosen) {
            return $a['id_dosen'] == $id_dosen;
        });

        usort($filtered_agenda, function ($a, $b) {
            return strtotime($a['tanggal']) - strtotime($b['tanggal']);
        });
        
        $data['agenda'] = $filtered_agenda;

        // --- Tambahkan baris ini untuk mengambil jadwal ujian ---
        $data['jadwal_ujian'] = $this->Agenda_model->get_confirmed_exams_by_dosen($id_dosen);
        // --- Akhir penambahan ---

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar_dosen', $data);
        $this->load->view('templates/navbar', $data);
        $this->load->view('agenda/index', $data);
        $this->load->view('templates/footer');
    }

    // ... (fungsi edit, edit_by_date, store_by_date, update Anda yang sudah ada) ...
    public function edit($id) {
        $id_dosen = $this->session->userdata('id_dosen');
        $agenda = $this->Agenda_model->get_by_id($id, $id_dosen);

        if (!$agenda) {
            show_error('Agenda tidak ditemukan atau bukan milik Anda.', 403);
            return;
        }

        $slots_data = $this->Agenda_model->get_slots_by_date_and_dosen_string($agenda['tanggal'], $id_dosen);
        $agenda['slot_waktu'] = !empty($slots_data['slot_waktu']) ? explode(',', $slots_data['slot_waktu']) : [];

        $data['title'] = 'Edit Agenda';
        $data['agenda'] = $agenda;
        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar_dosen', $data);
        $this->load->view('templates/navbar', $data);
        $this->load->view('agenda/edit', $data); 
        $this->load->view('templates/footer');
    }

    public function edit_by_date($tanggal) {
        $id_dosen = $this->session->userdata('id_dosen');
        if (!$id_dosen) {
            redirect('auth');
            return;
        }

        $agenda_data = $this->Agenda_model->get_by_date($tanggal, $id_dosen);

        if (!$agenda_data || empty($agenda_data['slot_waktu'])) {
            $agenda = [
                'id_agenda' => null, 
                'id_dosen' => $id_dosen,
                'tanggal' => $tanggal,
                'slot_waktu' => '' 
            ];
        } else {
            $agenda = [
                'id_agenda' => $agenda_data['id_agenda'], // Ambil ID dari get_by_date
                'id_dosen' => $id_dosen,
                'tanggal' => $tanggal,
                'slot_waktu' => $agenda_data['slot_waktu'] 
            ];
        }
        
        $data['title'] = 'Edit Ketersediaan per Tanggal';
        $data['agenda'] = $agenda;

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar_dosen', $data); 
        $this->load->view('templates/navbar', $data); 
        $this->load->view('agenda/agenda_edit_form', $data); 
        $this->load->view('templates/footer');
    }

    public function store_by_date() {
        $id_dosen = $this->session->userdata('id_dosen');
        if (!$id_dosen) {
            redirect('auth');
            return;
        }

        $tanggal = $this->input->post('tanggal');
        $slot_waktu_array = $this->input->post('slot_waktu') ?? [];
        sort($slot_waktu_array); // Urutkan slot waktu
        $slot_waktu_string = implode(',', $slot_waktu_array);
        // $id_agenda_hidden = $this->input->post('id_agenda'); 

        $data_to_save = [
            'id_dosen' => $id_dosen,
            'tanggal' => $tanggal,
            'slot_waktu' => $slot_waktu_string
        ];

        $existing_agenda = $this->Agenda_model->get_entry_by_date_dosen($tanggal, $id_dosen);

        if ($existing_agenda) {
            // Jika slot waktu kosong, hapus entri
            if (empty($slot_waktu_string)) {
                $this->Agenda_model->delete_agenda($existing_agenda['id_agenda']);
                $this->session->set_flashdata('message', 'Ketersediaan pada tanggal '.$tanggal.' berhasil dihapus.');
            } else {
                 // Jika id_agenda tidak ada di $existing_agenda, tambahkan. Model Anda mungkin perlu disesuaikan.
                $this->Agenda_model->update_agenda_by_date_dosen($tanggal, $id_dosen, ['slot_waktu' => $slot_waktu_string]);
                 $this->session->set_flashdata('message', 'Agenda berhasil diperbarui.');
            }
        } else {
            // Jika belum ada dan slot waktu tidak kosong, insert
            if (!empty($slot_waktu_string)) {
                $this->Agenda_model->insert_agenda($data_to_save);
                $this->session->set_flashdata('message', 'Agenda berhasil disimpan.');
            } else {
                 $this->session->set_flashdata('message', 'Tidak ada slot waktu yang dipilih, tidak ada agenda yang disimpan untuk tanggal '.$tanggal.'.');
            }
        }
        redirect('agenda');
    }

    public function update($id_agenda) 
    {
        $id_dosen_session = $this->session->userdata('id_dosen');

        $agenda_item = $this->Agenda_model->get_by_id($id_agenda, $id_dosen_session);
        if (!$agenda_item) {
            $this->session->set_flashdata('error', 'Agenda tidak ditemukan atau Anda tidak memiliki hak akses.');
            redirect('agenda');
            return;
        }

        $tanggal = $this->input->post('tanggal');
        $slot_waktu_array = $this->input->post('slot_waktu') ?? [];
        sort($slot_waktu_array); // Urutkan slot waktu
        $slot_waktu_string = implode(',', $slot_waktu_array);

        $data = [
            'tanggal' => $tanggal,
            'slot_waktu' => $slot_waktu_string
        ];

        if ($this->Agenda_model->update_agenda($id_agenda, $data)) {
             $this->session->set_flashdata('message', 'Agenda berhasil diperbarui.');
        } else {
             $this->session->set_flashdata('error', 'Gagal memperbarui agenda.');
        }
        redirect('agenda');
    }

    public function import_excel_form() {
        $data['title'] = 'Impor Agenda dari Excel';
        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar_dosen', $data); 
        $this->load->view('templates/navbar', $data); 
        $this->load->view('agenda/import_form', $data); 
        $this->load->view('templates/footer');
    }

    public function process_import_excel() {
        $id_dosen_login = $this->session->userdata('id_dosen');
        if (!$id_dosen_login) {
            $this->session->set_flashdata('error', 'Sesi tidak valid. Silakan login kembali.');
            redirect('auth'); 
            return;
        }
        
        $upload_path = './uploads/excel/';
        if (!is_dir($upload_path)) {
            mkdir($upload_path, 0777, TRUE);
        }

        $config['upload_path'] = $upload_path;
        $config['allowed_types'] = 'xlsx|xls';
        $config['max_size'] = 2048;
        $config['encrypt_name'] = TRUE;
        $this->upload->initialize($config);

        if (!$this->upload->do_upload('excel_file')) {
            $error = array('error' => $this->upload->display_errors());
            $this->session->set_flashdata('error', $error['error']);
            redirect('agenda/import_excel_form');
        } else {
            $file_data = $this->upload->data();
            $file_path = $file_data['full_path'];

            try {
                $spreadsheet = IOFactory::load($file_path);
                $sheet = $spreadsheet->getActiveSheet();
                $highestRow = $sheet->getHighestRow();

                $imported_count = 0;
                $skipped_rows = [];
                $updated_count = 0; 

                for ($row = 2; $row <= $highestRow; $row++) { 
                    $excel_tanggal = $sheet->getCell('A' . $row)->getValue();
                    $excel_slot_waktu_raw = $sheet->getCell('B' . $row)->getValue();

                    if (empty($excel_tanggal) && empty($excel_slot_waktu_raw) && $row == $highestRow && $highestRow > 2 && $imported_count == 0 && $updated_count == 0) {
                         // Baris benar-benar kosong di akhir file dan belum ada data yang diproses, abaikan.
                        if ($row == 2 && ($sheet->getCell('A3')->getValue() == null && $sheet->getCell('B3')->getValue() == null)){ // Jika baris 2 kosong dan baris 3 juga kosong
                             $skipped_rows[] = "File Excel tampak kosong atau tidak memiliki data pada baris yang diharapkan.";
                             break; // Keluar dari loop jika file tampak kosong
                        }
                        if ($row > 2 && $sheet->getCell('A'.($row-1))->getValue() != null ) {
                            // Jika baris sebelumnya ada data, maka baris kosong ini adalah akhir data.
                        } else if ($row == 2) {
                            // Kemungkinan file hanya header atau 1 baris data dan baris berikutnya kosong
                        } else {
                             continue; // Abaikan baris kosong di akhir
                        }
                    } else if (empty($excel_tanggal) || $excel_slot_waktu_raw === null) { // Periksa $excel_slot_waktu_raw secara eksplisit untuk null
                        $skipped_rows[] = "Baris $row: Data tanggal atau slot waktu tidak boleh kosong.";
                        continue;
                    }
                    
                    // Konversi tanggal
                    $tanggal_formatted = '';
                    if (is_numeric($excel_tanggal)) {
                        if ($excel_tanggal > 25569 && $excel_tanggal < 60000) { 
                            $UNIX_DATE = ($excel_tanggal - 25569) * 86400;
                            $tanggal_formatted = gmdate("Y-m-d", $UNIX_DATE);
                        } else {
                            $date_obj = DateTime::createFromFormat('Y-m-d', (string)$excel_tanggal);
                             if (!$date_obj || $date_obj->format('Y-m-d') !== (string)$excel_tanggal) {
                                $date_obj = DateTime::createFromFormat('d/m/Y', (string)$excel_tanggal);
                                if ($date_obj) {
                                    $tanggal_formatted = $date_obj->format('Y-m-d');
                                } else {
                                     $skipped_rows[] = "Baris $row: Format tanggal ($excel_tanggal) tidak valid. Gunakan YYYY-MM-DD atau DD/MM/YYYY.";
                                     continue;
                                }
                            } else {
                                $tanggal_formatted = $date_obj->format('Y-m-d');
                            }
                        }
                    } else {
                        $date_obj = DateTime::createFromFormat('Y-m-d', $excel_tanggal);
                        if ($date_obj && $date_obj->format('Y-m-d') === $excel_tanggal) {
                            $tanggal_formatted = $date_obj->format('Y-m-d');
                        } else {
                            $date_obj_slash = DateTime::createFromFormat('d/m/Y', $excel_tanggal);
                            if ($date_obj_slash) {
                                $tanggal_formatted = $date_obj_slash->format('Y-m-d');
                            } else {
                                $skipped_rows[] = "Baris $row: Format tanggal ($excel_tanggal) tidak valid. Gunakan YYYY-MM-DD atau DD/MM/YYYY.";
                                continue;
                            }
                        }
                    }

                    // Proses slot waktu: pastikan string, urutkan, dan gabungkan
                    $slot_waktu_array = array_map('trim', explode(',', (string)$excel_slot_waktu_raw));
                    $slot_waktu_array = array_filter($slot_waktu_array); // Hapus slot kosong jika ada
                    sort($slot_waktu_array);
                    $slot_waktu_string = implode(',', $slot_waktu_array);


                    $data_agenda = [
                        'id_dosen' => $id_dosen_login,
                        'tanggal' => $tanggal_formatted,
                        'slot_waktu' => $slot_waktu_string
                    ];

                    $existing_agenda = $this->Agenda_model->get_entry_by_date_dosen($tanggal_formatted, $id_dosen_login);

                    if ($existing_agenda) {
                        if (empty($slot_waktu_string)) { // Jika slot waktu di Excel kosong, hapus agenda
                            if ($this->Agenda_model->delete_agenda($existing_agenda['id_agenda'])) {
                                $updated_count++; // Anggap sebagai "update" karena entri dihapus
                            } else {
                                $skipped_rows[] = "Baris $row: Gagal menghapus data untuk tanggal $tanggal_formatted.";
                            }
                        } else {
                            if ($this->Agenda_model->update_agenda_by_date_dosen($tanggal_formatted, $id_dosen_login, ['slot_waktu' => $data_agenda['slot_waktu']])) {
                                $updated_count++;
                            } else {
                                $skipped_rows[] = "Baris $row: Gagal update data untuk tanggal $tanggal_formatted.";
                            }
                        }
                    } else {
                        if (!empty($slot_waktu_string)) { // Hanya insert jika ada slot waktu
                            if ($this->Agenda_model->insert_agenda($data_agenda)) {
                                $imported_count++;
                            } else {
                                $skipped_rows[] = "Baris $row: Gagal insert data untuk tanggal $tanggal_formatted.";
                            }
                        }
                    }
                }

                if (file_exists($file_path)) {
                    unlink($file_path);
                }

                $message = "";
                if ($imported_count > 0) {
                    $message .= "$imported_count data agenda baru berhasil diimpor. ";
                }
                if ($updated_count > 0) {
                    $message .= "$updated_count data agenda berhasil diperbarui/dihapus. ";
                }
                if ($imported_count == 0 && $updated_count == 0 && empty($skipped_rows)) {
                     $message = "Tidak ada data baru untuk diimpor atau diperbarui dari file, atau file hanya berisi header.";
                } else if ($imported_count == 0 && $updated_count == 0 && !empty($skipped_rows) && strpos($skipped_rows[0], "File Excel tampak kosong") !== false) {
                    $message = $skipped_rows[0]; // Tampilkan pesan spesifik jika file kosong
                    $this->session->set_flashdata('error', $message);
                    redirect('agenda');
                    return;
                } else if ($imported_count == 0 && $updated_count == 0 && !empty($skipped_rows)) {
                    $message = "Tidak ada data yang berhasil diimpor atau diperbarui.";
                }


                if (!empty($skipped_rows)) {
                    $message .= "<br>Beberapa baris dilewati/gagal:<br>" . implode("<br>", $skipped_rows);
                    $this->session->set_flashdata('error', $message); 
                } else {
                    if (!empty(trim($message))) { // Hanya set flashdata jika ada pesan yang berarti
                       $this->session->set_flashdata('message', $message);
                    } else {
                        // Jika tidak ada yang diimpor, diupdate, atau diskip, mungkin file kosong atau hanya header
                        $this->session->set_flashdata('message', "Tidak ada perubahan pada agenda dari file yang diunggah.");
                    }
                }

            } catch (Exception $e) {
                if (file_exists($file_path)) { // Hapus file jika ada error juga
                    unlink($file_path);
                }
                $this->session->set_flashdata('error', 'Error memproses file Excel: ' . $e->getMessage());
            }
            redirect('agenda');
        }
    }
}
?>