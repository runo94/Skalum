<?php
/**
 * Plugin Name: MS Multistep Form Builder
 * Description: Конструктор покрокових форм на ACF (steps + кастомні опції, entries + e-mail).
 * Author: Anton + ChatGPT
 * Version: 0.2.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Базові константи
define( 'MSMF_PATH', plugin_dir_path( __FILE__ ) );
define( 'MSMF_URL',  plugin_dir_url( __FILE__ ) );

// Підключаємо класи / ACF-конфіг
require_once MSMF_PATH . 'includes/class-ms-multistep-form.php';
require_once MSMF_PATH . 'includes/acf-form-fields.php';

// Ініціалізація плагіна
function msmf_init_plugin() {
    // один інстанс на сайті
    static $instance = null;

    if ( null === $instance ) {
        $instance = new MSMF_Plugin();
    }

    return $instance;
}
add_action( 'plugins_loaded', 'msmf_init_plugin' );
