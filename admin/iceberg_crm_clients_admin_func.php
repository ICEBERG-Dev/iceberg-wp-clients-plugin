<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/wp-load.php');


function iceberg_crm_clients_handle_token_authentication_clients() {
    if (isset($_POST['iceberg_crm_clients_token'])) {
      $token = sanitize_text_field($_POST['iceberg_crm_clients_token']);
      $response = iceberg_crm_clients_send_token_to_server_clients($token);

      if ($response == 'OK') {
        add_settings_error('iceberg_crm_clients', 'iceberg_crm_clients_message', __('Token was saved successfully.', 'iceberg_crm_clients'), 'updated');
      } elseif($response == 'wrongIdentifier') {
        $token = "";
        add_settings_error('iceberg_crm_clients', 'iceberg_crm_clients_message', __('Token already in use! Try another', 'iceberg_crm_clients'), 'iceberg_crm_clients');
      } elseif($response == 'badToken') {
        $token = "";
        add_settings_error('iceberg_crm_clients', 'iceberg_crm_clients_message', __('Incorrect token! Try another', 'iceberg_crm_clients'), 'iceberg_crm_clients');
      }
      else {
        $token = "";
        add_settings_error('iceberg_crm_clients', 'iceberg_crm_clients_message', __('Token was not saved. Server response: '.$response, 'iceberg_crm_clients'));
      }
      iceberg_crm_clients_store_token_in_database_clients($token);

    }
}

function iceberg_crm_clients_send_token_to_server_clients($token) {
    // URL of the endpoint to send the request to
    $url = ICEBERG_CRM_CLIENTS_HOST.':'.ICEBERG_CRM_CLIENTS_PORT.'/auth';

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

function iceberg_crm_clients_store_token_in_database_clients($token) {
    global $wpdb;
    $table_name = $wpdb->prefix . "iceberg_crm_clients_tokens";
    $data = array(
        'id'=>1,
        'token' => $token
    );
    $wpdb->replace($table_name, $data);
}

function iceberg_crm_clients_add_token_form_section_clients() {
    add_settings_section(
        'iceberg_crm_clients_section',
        __('Iceberg CRM Clients Settings', 'iceberg_crm_clients'),
        'iceberg_crm_clients_section_clients',
        'iceberg_crm_clients'
    );

    add_settings_field(
        'iceberg_crm_clients_token',
        __('Token', 'iceberg_crm_clients'),
        'iceberg_crm_clients_token_clients',
        'iceberg_crm_clients',
        'iceberg_crm_clients_section'
    );

    register_setting('iceberg_crm_clients', 'iceberg_crm_clients_token');
}

function iceberg_crm_clients_token_clients() {
    global $wpdb;
    $table_name = $wpdb->prefix . "iceberg_crm_clients_tokens";
    $token = $wpdb->get_var("SELECT token FROM $table_name");
    echo '<input type="text" name="iceberg_crm_clients_token" placeholder="Enter token to check and save..." value="'.esc_attr($token).'" class="regular-text">';
}

function iceberg_crm_clients_get_data_about_clients($token, $phone, $email, $id) {
    // URL of the endpoint to send the request to
    $url = ICEBERG_CRM_CLIENTS_HOST.':'.ICEBERG_CRM_CLIENTS_PORT.'/getClient';

    $wphost_id = parse_url(home_url())['host'];

    // Prepare the request data
    $data = array('token' => $token, 'phone' => $phone, 'email' => $email, 'id' => $id);

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

function iceberg_crm_clients_get_token_from_db( ) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'iceberg_crm_clients_tokens';

    // Query the database to get the token for the specified user
    $token = $wpdb->get_var( $wpdb->prepare(
        "SELECT token FROM $table_name"
    ) );

    return $token;
}


// Define shortcode function
function iceberg_crm_clients_client_bonus_balance_shortcode() {
    //get token
    $token = iceberg_crm_clients_get_token_from_db();
    if ($token) {
        //get email
        $current_user = wp_get_current_user();
        $user_email = $current_user->user_email;
        $email = get_user_by('email', $user_email);
        //get phone
        $user_id = $current_user->ID;
        $phone_number = get_user_meta($user_id, 'phone_number', true);
        if ($phone_number === "") {
            $phone_number = get_user_meta($user_id, 'billing_phone', true);
        }
        $phone = preg_replace('/[^0-9]/', '', $phone_number);
        //get id
        $crm_id = get_user_meta($current_user->ID, 'crmID', true);

        $data = json_decode(iceberg_crm_clients_get_data_about_clients($token, $phone, $email, $crm_id), true);

        if ($crm_id === "") {
            $crm_id = $data['id'];
            update_user_meta($current_user->ID, 'crmID', $crm_id);
        }

        if ($phone === "") {
            $phone = $data['phone_number'];
            update_user_meta($current_user->ID, 'phone_number', $phone);
            update_user_meta($current_user->ID, 'billing_phone', $phone);
        }

        return $data['balance'];
    } else {
        return "";
    }
}
