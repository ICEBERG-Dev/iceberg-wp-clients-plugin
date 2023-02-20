<?php
/*
Plugin Name: Iceberg CRM Callback
Description: Hey there! This is the ICEBERG crm reference!
Contributors: iceberg group
Author: Iceberg Group
Author URI: https://iceberg-crm.ru
Version: 0.1.0
*/

require_once plugin_dir_path( __FILE__ ) . 'themes/remote_params.php';
require_once plugin_dir_path( __FILE__ ) . 'admin/iceberg_crm_callback_admin_func.php';
require_once plugin_dir_path( __FILE__ ) . 'views/iceberg_crm_callback_views.php';

function iceberg_crm_callback_install() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'iceberg_crm_callback_tokens';
    $charset_collate = $wpdb->get_charset_collate();
 
    if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
        $sql = "CREATE TABLE $table_name (
          id mediumint(9) NOT NULL AUTO_INCREMENT,
          token text NOT NULL,
          PRIMARY KEY  (id)
        ) $charset_collate;";
 
        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        dbDelta( $sql );
    }

    $table_name = $wpdb->prefix . 'iceberg_crm_callback_links';
    $charset_collate = $wpdb->get_charset_collate();
 
    if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
        $sql = "CREATE TABLE $table_name (
          id mediumint(9) NOT NULL AUTO_INCREMENT,
          link text NOT NULL,
          PRIMARY KEY  (id)
        ) $charset_collate;";
 
        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        dbDelta( $sql );
    }
}
function iceberg_crm_callback_uninstall() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'iceberg_crm_callback_tokens';
    $wpdb->query("DROP TABLE IF EXISTS $table_name");

    $table_name = $wpdb->prefix . 'iceberg_crm_callback_links';
    $wpdb->query("DROP TABLE IF EXISTS $table_name");
}
 
add_action('admin_menu', 'add_iceberg_crm_callback_menu_page');
add_action('admin_init', 'add_token_form_section_callback');
add_action('admin_init', 'handle_token_authentication_callback');
add_action( 'wp_enqueue_scripts', 'iceberg_crm_callback_enqueue_scripts' );
add_action( 'wp_footer', 'iceberg_crm_callback_widget' );
register_activation_hook( __FILE__, 'iceberg_crm_callback_install' );
register_deactivation_hook( __FILE__, 'iceberg_crm_callback_uninstall' );
