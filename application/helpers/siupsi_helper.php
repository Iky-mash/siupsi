<?php

function if_logged_in()
{
    $ci = get_instance();
    if (!$ci->session->userdata('email')){
        redirect('auth');
    } else {
        
    }

    
}

function check_role($allowed_roles) {
    $CI = &get_instance();
    $user_role = $CI->session->userdata('role'); // Ambil role dari session
    if (!in_array($user_role, $allowed_roles)) {
        show_error('Anda tidak memiliki izin untuk mengakses halaman ini.', 403, 'Forbidden');
    }
}

?>