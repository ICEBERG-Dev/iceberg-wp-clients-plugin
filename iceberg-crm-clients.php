<?php
/*
Plugin Name: Iceberg CRM Clients
Description: Hey there! This is the ICEBERG crm reference!
Contributors: iceberg group
Author: Iceberg Group
Author URI: https://iceberg-crm.ru
Version: 0.1.0
*/

require_once plugin_dir_path( __FILE__ ) . 'themes/remote_params.php';
require_once plugin_dir_path( __FILE__ ) . 'admin/iceberg_crm_clients_admin_func.php';
require_once plugin_dir_path( __FILE__ ) . 'views/iceberg_crm_clients_views.php';

function iceberg_crm_clients_install() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'iceberg_crm_clients_tokens';
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
}
function iceberg_crm_clients_uninstall() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'iceberg_crm_clients_tokens';
    $wpdb->query("DROP TABLE IF EXISTS $table_name");

    $table_name = $wpdb->prefix . 'iceberg_crm_clients_links';
    $wpdb->query("DROP TABLE IF EXISTS $table_name");
}

add_action('admin_menu', 'add_iceberg_crm_clients_menu_page');
add_action('admin_init', 'iceberg_crm_clients_add_token_form_section_clients');
add_action('admin_init', 'iceberg_crm_clients_handle_token_authentication_clients');
register_activation_hook( __FILE__, 'iceberg_crm_clients_install' );
register_deactivation_hook( __FILE__, 'iceberg_crm_clients_uninstall' );
add_shortcode( 'clientBonusBalance', 'iceberg_crm_clients_client_bonus_balance_shortcode' );
