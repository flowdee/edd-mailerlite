<?php
/**
 * Register our settings section
 *
 * @return array
 */
function edd_ml_settings_section( $sections ) {

    $sections['edd-ml-settings'] = __( 'Mailerlite', 'edd-mailerlite' );

    return $sections;
}
add_filter( 'edd_settings_sections_extensions', 'edd_ml_settings_section' );

/**
 * Register our settings
 *
 * @return array
 */
function edd_ml_settings( $settings ) {

    $ml_settings = array(
        array(
            'id'   => 'edl_ml',
            'name' => '<strong>' . __( 'Mailerlite', 'edd-mailerlite' ) . '</strong>',
            'desc' => '',
            'type' => 'header',
            'size' => 'regular'
        ),
        array(
            'id'    => 'edd_ml_api_key',
            'name'  => __( 'Mailerlite API Key', 'edd-mailerlite' ),
            'desc'  => edd_ml_settings_get_api_key_status(),
            'type'  => 'text'
        ),
        array(
            'id'    => 'edd_ml_group',
            'name'  => __( 'Group', 'edd-mailerlite' ),
            'desc'  => __( 'The default group which will be taken for new subscribers', 'edd-mailerlite' ),
            'type'  => 'select',
            'options' => edd_ml_settings_get_group_options()
        ),
        array(
            'id'    => 'edd_ml_checkout',
            'name'  => __( 'Checkout', 'edd-mailerlite' ),
            'desc'  => __( 'Enable list subscription via checkout page', 'edd-mailerlite' ),
            'type'  => 'checkbox'
        ),
        array(
            'id'    => 'edd_ml_checkout_preselect',
            'name'  => __( 'Pre-select checkbox', 'edd-mailerlite' ),
            'desc'  => __( 'Check in order to pre-select the signup checkbox by default', 'edd-mailerlite' ),
            'type'  => 'checkbox'
        ),
        array(
            'id'    => 'edd_ml_checkout_hide',
            'name'  => __( 'Hide checkbox', 'edd-mailerlite' ),
            'desc'  => __( 'Check in order to hide the checkbox. All customers will be subscribed automatically', 'edd-mailerlite' ),
            'type'  => 'checkbox'
        ),
        array(
            'id'    => 'edd_ml_checkout_label',
            'name'  => __( 'Checkbox label', 'edd-mailerlite' ),
            'desc'  => __( 'The text which will be shown besides the checkbox', 'edd-mailerlite' ),
            'type'  => 'text',
            'std'   => __( 'Yes, I want to receive your newsletter.', 'edd-mailerlite' )
        ),
        array(
            'id'    => 'edd_ml_double_optin',
            'name'  => __( 'Double Opt-In', 'edd-mailerlite' ),
            'desc'  => __( 'Check in order to force email confirmation before being added to your list', 'edd-mailerlite' ),
            'type'  => 'checkbox'
        ),
    );

    $ml_settings = apply_filters( 'edd_ml_settings', $ml_settings );

    // If EDD is at version 2.5 or later...
    if ( version_compare( EDD_VERSION, 2.5, '>=' ) ) {
        $ml_settings = array( 'edd-ml-settings' => $ml_settings );
    }

    return array_merge( $settings, $ml_settings );
}
add_filter( 'edd_settings_extensions', 'edd_ml_settings' );

/**
 * Validate settings
 *
 * @param $input
 * @return mixed
 */
function edd_ml_settings_validation( $input ) {

    if ( isset( $input['edd_ml_api_key'] ) ) {

        $api_status = edd_get_option( 'edd_ml_api_status', false );
        $api_key = edd_get_option( 'edd_ml_api_key', '' );

        if ( empty( $input['edd_ml_api_key'] ) ) {
            $api_status = false;

        } elseif ( ! empty( $input['edd_ml_api_key'] ) && $input['edd_ml_api_key'] != $api_key ) {

            $validation = edd_ml_validate_api_key( esc_html( $input['edd_ml_api_key'] ) );

            $api_status = ( $validation );
        }

        // Store API validation
        $input['edd_ml_api_status'] = $api_status;

        // Reset groups when saving options
        if ( $api_status )
            delete_transient( 'edd_ml_groups' );
    }

    return $input;
}
add_filter( 'edd_settings_extensions_sanitize', 'edd_ml_settings_validation' );