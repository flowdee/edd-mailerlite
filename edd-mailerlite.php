<?php
/**
 * Plugin Name:     EDD Mailerlite
 * Plugin URI:      https://wordpress.org/plugins/edd-mailerlite/
 * Description:     Mailerlite integration for Easy Digital Downloads
 * Version:         1.0.0
 * Author:          flowdee
 * Author URI:      https://flowdee.de
 * Text Domain:     edd-mailerlite
 *
 * @author          flowdee
 * @copyright       Copyright (c) flowdee
 *
 * Copyright (c) 2017 - flowdee ( https://twitter.com/flowdee )
 */

// Exit if accessed directly
if( ! defined( 'ABSPATH' ) ) exit;

if( ! class_exists( 'EDD_Mailerlite' ) ) {

    /**
     * Main EDD_Mailerlite class
     *
     * @since       1.0.0
     */
    class EDD_Mailerlite {

        /**
         * @var         EDD_Mailerlite $instance The one true EDD_Mailerlite
         * @since       1.0.0
         */
        private static $instance;


        /**
         * Get active instance
         *
         * @access      public
         * @since       1.0.0
         * @return      object self::$instance The one true EDD_Mailerlite
         */
        public static function instance() {
            if( !self::$instance ) {
                self::$instance = new EDD_Mailerlite();
                self::$instance->setup_constants();
                self::$instance->includes();
                self::$instance->load_textdomain();
            }

            return self::$instance;
        }


        /**
         * Setup plugin constants
         *
         * @access      private
         * @since       1.0.0
         * @return      void
         */
        private function setup_constants() {

            // Plugin name
            define( 'EDD_MAILERLITE_NAME', 'EDD Mailerlite' );

            // Plugin version
            define( 'EDD_MAILERLITE_VER', '1.0.0' );

            // Plugin path
            define( 'EDD_MAILERLITE_DIR', plugin_dir_path( __FILE__ ) );

            // Plugin URL
            define( 'EDD_MAILERLITE_URL', plugin_dir_url( __FILE__ ) );

            // Plugin prefix
            define( 'EDD_MAILERLITE_PREFIX', 'edd_ml_' );

            // API Key
            $api_key = ( function_exists( 'edd_get_option' ) ) ? edd_get_option( 'edd_ml_api_key', '' ) : '';
            define( 'FLOWDEE_MAILERLITE_API_KEY', $api_key );
        }
        
        /**
         * Include necessary files
         *
         * @access      private
         * @since       1.0.0
         * @return      void
         */
        private function includes() {

            // Get out if EDD is not active
            if ( ! function_exists( 'EDD' ) ) {
                return;
            }

            // Dependencies
            require_once EDD_MAILERLITE_DIR . 'vendor/autoload.php';
            require_once EDD_MAILERLITE_DIR . 'includes/shared/flowdee-mailerlite-functions.php';

            // Admin only
            if ( is_admin() ) {
                //require_once EDD_MAILERLITE_DIR . 'includes/admin/plugins.php';
                require_once EDD_MAILERLITE_DIR . 'includes/admin/settings.php';
            }

            // Anything else
            require_once EDD_MAILERLITE_DIR . 'includes/functions.php';
            require_once EDD_MAILERLITE_DIR . 'includes/hooks.php';
        }

        /**
         * Internationalization
         *
         * @access      public
         * @since       1.0.0
         * @return      void
         */
        public function load_textdomain() {
            // Set filter for language directory
            $lang_dir = EDD_MAILERLITE_DIR . '/languages/';
            $lang_dir = apply_filters( 'edd_mailerlite_languages_directory', $lang_dir );

            // Traditional WordPress plugin locale filter
            $locale = apply_filters( 'plugin_locale', get_locale(), 'edd-mailerlite' );
            $mofile = sprintf( '%1$s-%2$s.mo', 'edd-mailerlite', $locale );

            // Setup paths to current locale file
            $mofile_local   = $lang_dir . $mofile;
            $mofile_global  = WP_LANG_DIR . '/edd-mailerlite/' . $mofile;

            if( file_exists( $mofile_global ) ) {
                // Look in global /wp-content/languages/edd-mailerlite/ folder
                load_textdomain( 'edd-mailerlite', $mofile_global );
            } elseif( file_exists( $mofile_local ) ) {
                // Look in local /wp-content/plugins/edd-mailerlite/languages/ folder
                load_textdomain( 'edd-mailerlite', $mofile_local );
            } else {
                // Load the default language files
                load_plugin_textdomain( 'edd-mailerlite', false, $lang_dir );
            }
        }
    }
} // End if class_exists check

/**
 * The main function responsible for returning the one true EDD_Mailerlite
 * instance to functions everywhere
 * @since       1.0.0
 * @return      \EDD_Mailerlite The one true EDD_Mailerlite
 *
 */
function edd_ml_load() {
    return EDD_Mailerlite::instance();
}
add_action( 'plugins_loaded', 'edd_ml_load' );