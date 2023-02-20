<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/wp-load.php');

$link = $_POST['link'];
function store_link_in_db($link) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'iceberg_crm_callback_links';

    $existing_link = $wpdb->get_var("SELECT link FROM $table_name");
    $wpdb->query("DELETE FROM $table_name");
    $wpdb->insert(
        $table_name,
        array('link' => $link),
        array('%s')
    );

}
store_link_in_db($link);

header('Location: ' . admin_url('options-general.php?page=iceberg_crm_callback'));
exit;
