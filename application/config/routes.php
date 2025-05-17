<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	https://codeigniter.com/userguide3/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There are three reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router which controller/method to use if those
| provided in the URL cannot be matched to a valid route.
|
|	$route['translate_uri_dashes'] = FALSE;
|
| This is not exactly a route, but allows you to automatically route
| controller and method names that contain dashes. '-' isn't a valid
| class or method name character, so it requires translation.
| When you set this option to TRUE, it will replace ALL dashes in the
| controller and method URI segments.
|
| Examples:	my-controller/index	-> my_controller/index
|		my-controller/my-method	-> my_controller/my_method
*/
$route['default_controller'] = 'auth';
$route['admin'] = 'admin/index';  // Halaman utama admin
$route['admin/assign_pembimbing'] = 'admin/assign_pembimbing'; 
$route['mahasiswa/edit/(:num)'] = 'mahasiswa/edit/$1';
$route['admin/pengajuan_ujian'] = 'admin/pengajuan_ujian'; // jika ingin langsung ke method 'pengajuan_ujian' di controller 'admin'
$route['dosen/rekomendasiJadwal/(:num)'] = 'dosen/rekomendasiJadwal/$1';
$route['excel_import'] = 'Excel_import';
$route['dosen/setujui-jadwal/(:num)'] = 'dosen/setujui_jadwal/$1';
$route['admin/agenda/(:num)'] = 'admin/agenda/$1';
$route['admin/rekomendasi_jadwal/(:num)'] = 'admin/rekomendasi_jadwal/$1';
$route['ruangan'] = 'ruangan/index';
$route['agenda/store'] = 'agenda/store_by_date';
$route['agenda/update/(:num)'] = 'agenda/update/$1';
$route['admin/agenda/(:num)'] = 'admin/index/$1';
$route['dosen/profil/(:num)'] = 'dosen/profil/$1';  // Menangkap ID dosen
$route['admin/rekomendasi'] = 'admin/rekomendasi';
$route['pengajuan'] = 'pengajuan/index';      // tampilkan form pengajuan
$route['pengajuan/submit'] = 'pengajuan/submit';  // submit draft pengajuan
$route['pengajuan/konfirmasi'] = 'pengajuan/konfirmasi'; // konfirmasi pengajuan
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;
