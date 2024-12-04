<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pengajuan extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('Pengajuan_model');
        $this->load->library('form_validation');
        check_role(['Admin', 'Mahasiswa']); 

    }

    public function form() {
        // Cek apakah mahasiswa sudah login
        if (!$this->session->userdata('role_id') == 3) { // Role ID 3 untuk mahasiswa
            redirect('auth');
        }

        $data['title'] = 'Pengajuan Ujian Skripsi';

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar_mahasiswa', $data);
        $this->load->view('templates/navbar', $data);
        $this->load->view('pengajuan/form', $data);
        $this->load->view('templates/footer');
    }

    public function submit() {
        // Validasi input
        $this->form_validation->set_rules('judul_skripsi', 'Judul Skripsi', 'required|trim');
        if (empty($_FILES['lembar_pengesahan']['name'])) {
            $this->form_validation->set_rules('lembar_pengesahan', 'Lembar Pengesahan', 'required');
        }

        if ($this->form_validation->run() == false) {
            $this->form();
        } else {
            $upload_config = [
                'upload_path'   => FCPATH . './assets/file', 
                'allowed_types' => 'pdf',
                'max_size'      => 2048,
            ];
            $this->upload->initialize($upload_config);

            if (!$this->upload->do_upload('lembar_pengesahan')) {
                $this->session->set_flashdata('message', '<div class="alert alert-danger">Gagal mengunggah lembar pengesahan: ' . $this->upload->display_errors() . '</div>');
                redirect('pengajuan/form');
            } else {
                $file_data = $this->upload->data();
                $data = [
                    'mahasiswa_id' => $this->session->userdata('id'),
                    'judul_skripsi' => $this->input->post('judul_skripsi'),
                    'lembar_pengesahan' => $file_data['file_name'],
                    'status' => 'Diajukan',
                ];

                $this->Pengajuan_model->insert($data);
                $this->session->set_flashdata('message', '<div class="alert alert-success">Pengajuan berhasil diajukan.</div>');
                redirect('pengajuan/form');
            }
        }
    }
}
