<?php

// function check_user_access() {
//     $CI =& get_instance();
//     $CI->load->library('session');

//     // Mendapatkan role user
//     $role = $CI->session->userdata('role');

//     // Mendapatkan current URI
//     $current_route = $CI->uri->uri_string();

//     // Daftar route dan peran yang diizinkan
//     $allowed_routes = [
//         'admin' => ['admin/dashboard', 'admin/users'],
//         'user'  => ['user/dashboard', 'user/profile']
//     ];

//     // Validasi akses
//     if (!isset($allowed_routes[$role]) || 
//         !in_array($current_route, $allowed_routes[$role])) {
//         show_error('Unauthorized access', 403);
//     }
// }


?>