<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Main admin class
 *
 * @author      Wes Cole
 * @category    Class
 * @package     ObjectivGoogleMaps/Classes
 * @since       1.0
 */
class Obj_Gmaps_Admin {

    public function __construct( $file ) {

        $this->file = $file;

        // Activation and Deactivation Hooks
        register_activation_hook( $file, array( $this, 'activate_plugin' ) );
		register_deactivation_hook( $file, array( $this, 'deactivate_plugin' ) );

    }

    /**
     * Activation callback
     */
    public function activate_plugin() {
        flush_rewrite_rules();
    }

    /**
     * Deactivation Callback
     */
    public function deactivate_plugin() {
        flush_rewrite_rules();
    }

}
