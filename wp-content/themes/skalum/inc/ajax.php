<?php
add_action('wp_ajax_skalum_demo', 'skalum_demo');
add_action('wp_ajax_nopriv_skalum_demo', 'skalum_demo');

function skalum_demo() {
    wp_send_json_success(['message' => 'AJAX connected!']);
}


add_action('wp_ajax_blog_more', 'blog_more');
add_action('wp_ajax_nopriv_blog_more', 'blog_more');
function blog_more() {
    $paged = $_GET['page'] + 1;

    $q = new WP_Query([
        'post_type' => 'post',
        'paged'     => $paged,
    ]);

    while ($q->have_posts()) {
        $q->the_post();
        get_template_part('template-parts/blog/card');
    }

    wp_die();
}