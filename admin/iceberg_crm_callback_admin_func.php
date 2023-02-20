<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/wp-load.php');


function handle_token_authentication_callback() {
    if (isset($_POST['iceberg_crm_callback_token'])) {
      $token = sanitize_text_field($_POST['iceberg_crm_callback_token']);
      $response = send_token_to_server_callback($token);
      

      if ($response == 'OK') {
        add_settings_error('iceberg_crm_callback', 'iceberg_crm_callback_message', __('Token was saved successfully.', 'iceberg_crm_callback'), 'updated');
      } elseif($response == 'wrongIdentifier') {
        $token = "";
        add_settings_error('iceberg_crm_callback', 'iceberg_crm_callback_message', __('Token already in use! Try another', 'iceberg_crm_callback'), 'iceberg_crm_callback');
      } elseif($response == 'badToken') {
        $token = "";
        add_settings_error('iceberg_crm_callback', 'iceberg_crm_callback_message', __('Incorrect token! Try another', 'iceberg_crm_callback'), 'iceberg_crm_callback');
      }
      else {
        $token = "";
        add_settings_error('iceberg_crm_callback', 'iceberg_crm_callback_message', __('Token was not saved. Server response: '.$response, 'iceberg_crm_callback'));
      }
      store_token_in_database_callback($token);

    }
}
  
function send_token_to_server_callback($token) {
    // URL of the endpoint to send the request to
    $url = HOST.':'.PORT.'/auth';

    $wphost_id = parse_url(home_url())['host'];

    // Prepare the request data
    $data = array('token' => $token, 'identifier' => $wphost_id);

    // Use WordPress built-in HTTP functions to send the request
    $response = wp_remote_post($url, array(
        'method' => 'GET',
        'timeout' => 45,
        'redirection' => 5,
        'httpversion' => '1.0',
        'blocking' => true,
        'headers' => array(),
        'body' => $data,
        'cookies' => array()
    ));

    // Check if the request was successful
    if (is_wp_error($response)) {
        // Handle error
        $error_message = $response->get_error_message();
        return 'ERROR: ' . $error_message;
    } else {
        // Return the response from the server
        return $response['body'];
    }
}

function store_token_in_database_callback($token) {
    global $wpdb;
    $table_name = $wpdb->prefix . "iceberg_crm_callback_tokens";
    $data = array(
        'id'=>1,
        'token' => $token
    );
    $wpdb->replace($table_name, $data);
}

function add_token_form_section_callback() {
    add_settings_section(
        'iceberg_crm_callback_section',
        __('Iceberg CRM Callback Settings', 'iceberg_crm_callback'),
        'iceberg_crm_callback_section_callback',
        'iceberg_crm_callback'
    );

    add_settings_field(
        'iceberg_crm_callback_token',
        __('Token', 'iceberg_crm_callback'),
        'iceberg_crm_callback_token_callback',
        'iceberg_crm_callback',
        'iceberg_crm_callback_section'
    );

    register_setting('iceberg_crm_callback', 'iceberg_crm_callback_token');
}

function iceberg_crm_callback_token_callback() {
    global $wpdb;
    $table_name = $wpdb->prefix . "iceberg_crm_callback_tokens";
    $token = $wpdb->get_var("SELECT token FROM $table_name");
    echo '<input type="text" name="iceberg_crm_callback_token" placeholder="Enter token to check and save..." value="'.esc_attr($token).'" class="regular-text">';
}
