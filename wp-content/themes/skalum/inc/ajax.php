<?php
add_action('wp_ajax_skalum_demo', 'skalum_demo');
add_action('wp_ajax_nopriv_skalum_demo', 'skalum_demo');

function skalum_demo() {
    wp_send_json_success(['message' => 'AJAX connected!']);
}
