<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Kabag extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->library('form_validation');
        $this->load->model('Pengajuan_model'); 
        $this->load->model('Mahasiswa_model');
        $this->load->model('Penjadwalan_model'); 
        $this->load->model('Dosen_model');
        $this->load->model('Agenda_model');
        $this->load->library('session');  
        $this->load->helper('url');  
        $this->load->model('Pekan_model');
        $this->load->model('Ruangan_model');
        if_logged_in();
        check_role(['Kabag']);

    }
    public function index() {
        $data['title'] = 'Dashboard Akademik';
    
        
        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar_kabag', $data);
        $this->load->view('templates/navbar', $data);
        $this->load->view('kabag/index', $data);
        $this->load->view('templates/footer');
    }

        public function pengajuan_ruangan() {
        $data['title'] = 'Jadwal Ujian';
      $data['jadwal'] = $this->Penjadwalan_model->get_all_jadwal();
    
        // Tampilkan ke view
        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar_dosen', $data);
        $this->load->view('templates/navbar', $data);
        $this->load->view('kabag/pengajuan_ruangan', $data);
        $this->load->view('templates/footer');
    }

}