<?php
/**
 * Shows the final purchase total at the bottom of the checkout page
 *
 * @since 1.5
 * @return void
 */
function edd_ml_checkout_label() {

    if ( ! edd_ml_is_active() )
        return;

    $checkout = edd_get_option( 'edd_ml_checkout', false );

    if ( ! $checkout )
        return;

    $group = edd_get_option( 'edd_ml_group' );

    if ( empty( $group ) )
        return;

    $label = edd_get_option( 'edd_ml_checkout_label' );
    $preselect = edd_get_option( 'edd_ml_checkout_preselect', false );
    $hidden = edd_get_option( 'edd_ml_checkout_hide', false );

    if ( $hidden ) {
        ?>
        <input name="edd_ml_subscribe" type="hidden" id="edd_ml_subscribe" value="1" checked="checked" />
        <?php
    } else { ?>
        <p id="edd-ml-subscribe">
            <input name="edd_ml_subscribe" type="checkbox" id="edd_ml_subscribe"
                   value="1" <?php if ($preselect) echo 'checked="checked"'; ?> />
            <label for="edd_ml_subscribe"><?php echo stripslashes( $label) ; ?></label>
        </p>
    <?php }
}
add_action( 'edd_purchase_form_before_submit', 'edd_ml_checkout_label', 1000 );

/**
 * Maybe prepare signup
 *
 * @param $payment_meta
 * @return mixed
 */
function edd_ml_checkout_maybe_prepare_signup( $payment_meta ) {

    if ( did_action( 'edd_purchase' ) ) {

        if ( isset( $_POST['edd_ml_subscribe'] ) && '1' == $_POST['edd_ml_subscribe'] ) {
            $payment_meta['edd_ml_subscribe'] = true;
        }

    }

    return $payment_meta;
}
add_filter( 'edd_payment_meta', 'edd_ml_checkout_maybe_prepare_signup');

/**
 * Maybe initiate signup after purchase completed
 *
 * @param $payment_id
 */
function edd_ml_maybe_initiate_signup( $payment_id ) {

    $payment_meta = edd_get_payment_meta( $payment_id );

    if ( isset( $payment_meta['edd_ml_subscribe'] ) && '1' == $payment_meta['edd_ml_subscribe'] ) {
        edd_ml_process_signup( $payment_id );
    }

}
add_action( 'edd_complete_purchase', 'edd_ml_maybe_initiate_signup', 10, 3 );