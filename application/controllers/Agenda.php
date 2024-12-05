<?php
defined('BASEPATH') or exit('No direct script access allowed');


class Agenda extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->library('form_validation');
        $this->load->library('session');  // Pastikan session di-load
        $this->load->helper('url');       // Helper untuk redirect
        if_logged_in();
        check_role(['Dosen']);
    }
    public function index() {
        $data['title'] = 'Dashboard Dosen';
        $this->load->model('Agenda_model');
        $id_dosen = $this->session->userdata('id_dosen'); // ID dosen yang login
        $data['agenda'] = $this->Agenda_model->get_agenda_by_dosen($id_dosen);
        

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar_dosen', $data);
        $this->load->view('templates/navbar', $data);
        $this->load->view('agenda/index', $data);
        $this->load->view('templates/footer');
    }

    public function add() {
       
        $data['title'] = 'Dashboard Dosen';
      

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar_dosen', $data);
        $this->load->view('templates/navbar', $data);
        $this->load->view('agenda/add', $data);
        $this->load->view('templates/footer');
    }

    public function store() {
        $this->load->model('Agenda_model');
        $id_dosen = $this->session->userdata('id_dosen'); // Ambil id_dosen dari session
        
        // Periksa apakah id_dosen ada
        if (!$id_dosen) {
            show_error('Session id_dosen tidak ditemukan.', 500);
        }
    
        $data = [
            'id_dosen' => $this->session->userdata('id_dosen'),
            'tanggal' => $this->input->post('tanggal'),
            'waktu_mulai' => $this->input->post('waktu_mulai'),
            'waktu_selesai' => $this->input->post('waktu_selesai'),
            'keterangan' => $this->input->post('keterangan'),
        ];
        $this->Agenda_model->insert_agenda($data);
        redirect('agenda');
    }

    public function edit($id_agenda) {
        $this->load->model('Agenda_model');
        $data['agenda'] = $this->Agenda_model->get_agenda_by_id($id_agenda);
        $this->load->view('agenda/edit', $data);
    }

    public function update($id_agenda) {
        $this->load->model('Agenda_model');
        $data = [
            'tanggal' => $this->input->post('tanggal'),
            'waktu_mulai' => $this->input->post('waktu_mulai'),
            'waktu_selesai' => $this->input->post('waktu_selesai'),
            'keterangan' => $this->input->post('keterangan'),
        ];
        $this->Agenda_model->update_agenda($id_agenda, $data);
        redirect('agenda');
    }

    public function delete($id_agenda) {
        $this->load->model('Agenda_model');
        $this->Agenda_model->delete_agenda($id_agenda);
        redirect('agenda');
    }
}
