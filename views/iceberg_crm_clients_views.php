<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/wp-load.php');

function get_iceberg_crm_clients_link() {
    global $wpdb;
    $table_name = $wpdb->prefix . "iceberg_crm_clients_links";
    $query = "SELECT link FROM $table_name LIMIT 1";
    $result = $wpdb->get_var($query);
    return $result;
}


function add_iceberg_crm_clients_menu_page() {
    add_options_page(
        'Set up your Iceberg Clients!',
        'Iceberg Clients',
        'manage_options',
        'iceberg_crm_clients',
        'iceberg_crm_clients_settings_page_clients'
    );
}
function iceberg_crm_clients_settings_page_clients() {
    ?>
    <div class="wrap">
        <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
        <form action="options.php" method="post" style="padding: 5px 25px;background-color: rgb(232, 232, 232);border-radius: 10px;margin: 10px 0px">
        <?php
            settings_errors('iceberg_crm_clients');
            do_settings_sections('iceberg_crm_clients');
            submit_button();
        ?>
        </form>
        <div style="padding: 5px 25px;background-color: rgb(232, 232, 232);border-radius: 10px;margin: 10px 0px;">
            <h2>Use this Shortcode on your page to display client's balance!</h2>
            <div style="display: flex; flex-direction: row;">
                <pre style="margin-right: 10px">[clientBonusBalance]</pre>
            </div>
        </div>
    </div>
    <?php
}


