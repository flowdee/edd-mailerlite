<?php
/**
 * Check if checkout action is active
 *
 * @return mixed
 */
function edd_ml_is_active() {
    $api_status = edd_get_option( 'edd_ml_api_status', false );

    return $api_status;
}

/**
 * Get settings api key status
 *
 * @return string
 */
function edd_ml_settings_get_api_key_status() {

    $api_status = edd_get_option( 'edd_ml_api_status', false );

    return ( $api_status ) ? '<span style="color: green;">' . __('Valid', 'edd-mailerlite' ) . '</span>' : '<span style="color: red;">' . __('Invalid', 'edd-mailerlite' ) . '</span>';
}

/**
 * Get settings group options
 *
 * @return array
 */
function edd_ml_settings_get_group_options() {

    $options = array();

    $groups = get_transient( 'edd_ml_groups' );

    if ( empty( $groups ) ) {
        $groups = flowdee_ml_get_groups();

        if ( ! empty( $groups ) )
            set_transient( 'edd_ml_groups', $groups, 60 * 60 * 24 ); // 24 hours
    }

    // Groups found
    if ( is_array( $groups ) && sizeof( $groups ) > 0 ) {

        $options[''] = __('Please select...', 'edd-mailerlite' );

        foreach ( $groups as $group ) {

            if ( isset( $group['id'] ) &&  isset( $group['name'] ) ) {
                $options[$group['id']] = $group['name'];
            }
        }

    // No groups found
    } else {
        $options[''] = __('No groups found', 'edd-mailerlite' );
    }

    return $options;
}

/**
 * Validate given API key
 *
 * @param $api_key
 * @return bool
 */
function edd_ml_validate_api_key( $api_key ) {

    if ( empty( $api_key ) )
        return false;

    $validation = flowdee_ml_api_key_validation( $api_key );

    return $validation;
}

/**
 * Process group signup(s)
 *
 * @param $payment_id
 */
function edd_ml_process_signup( $payment_id ) {

    $group = edd_get_option( 'edd_ml_group' );

    if ( empty( $group ) )
        return;

    $payment_meta = edd_get_payment_meta( $payment_id );

    if ( empty( $payment_meta['user_info']['email'] ) )
        return;

    $double_option = edd_get_option( 'edd_ml_double_optin', false );

    $subscriber = array(
        'email' => $payment_meta['user_info']['email'],
        'fields' => array(
            'name' => ( isset( $payment_meta['user_info']['first_name'] ) ) ? $payment_meta['user_info']['first_name'] : '',
            'last_name' => ( isset( $payment_meta['user_info']['last_name'] ) ) ? $payment_meta['user_info']['last_name'] : '',
        ),
        'type' => ( $double_option ) ? 'unconfirmed' : 'subscribed' // subscribed, active, unconfirmed
    );

    //edd_ml_debug_log( $subscriber );

    $added = flowdee_ml_add_subscriber( $group, $subscriber );

    if ( $added )
        edd_insert_payment_note( $payment_id, __( 'Customer successfully subscribed to mailing list(s).', 'edd-mailerlite' ) );
}

/**
 * Debug
 *
 * @param $args
 * @param bool $title
 */
function edd_ml_debug( $args, $title = false ) {

    if ( $title )
        echo '<h3>' . $title . '</h3>';

    echo '<pre>';
    print_r( $args );
    echo '</pre>';
}

/**
 * Debug to log file
 *
 * @param $message
 */
function edd_ml_debug_log( $message ) {

    if ( WP_DEBUG === true ) {
        if (is_array( $message ) || is_object( $message ) ) {
            error_log( print_r( $message, true ) );
        } else {
            error_log( $message );
        }
    }
}