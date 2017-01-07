<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Settings class
 *
 * @author      Wes Cole
 * @category    Class
 * @package     ObjectivGoogleMaps/Classes
 * @since       1.0
 */
class Obj_Gmaps_Settings {

    public function __construct() {

        if ( is_admin() ) {
            add_action( 'admin_menu', array( $this, 'add_settings_page' ) );
        }

    }

    /**
     * Create settings menu callback
     *
     * @since 1.0
     */
    public function add_settings_page() {
        add_submenu_page(
            'options-general.php',
            __( 'Google Map Settings', 'obj-google-maps' ),
            __( 'Google Maps', 'obj-google-maps' ),
            'manage_options',
            'obj_google_map_settings',
            array( $this, 'settings_page_markup' )
        );
    }

    /**
     * Settings page output
     *
     * @since 1.0
     */
    public function settings_page_markup() {
        ?>
        <div class="wrap">
            <h2><?php echo esc_html( get_admin_page_title() ); ?></h2>
        </div>
        <?php
    }

}
