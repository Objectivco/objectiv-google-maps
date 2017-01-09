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

		if ( is_admin() ) {
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_js' ) );
			add_action( 'admin_init', array( $this, 'register_metaboxes' ) );
		}

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

	/**
     * Enqueue JS
     *
     * @since 1.0
     */
    public function enqueue_js( $hook ) {

		$data_array = array(
			'api_key'	=> get_option( 'obj_api_key' )
		);

		if ( $hook == 'settings_page_obj_google_map_settings' ) {
			wp_enqueue_script( 'obj-google-maps-admin', plugins_url( '/assets/js/admin/build/main.js', $this->file ), array(), $this->version, true );
			wp_localize_script( 'obj-google-maps-admin', 'data', $data_array );
		}

    }

	/**
	 * Register Metaboxes for specified post type
	 *
	 * @since 1.0
	 */
	public function register_metaboxes() {
		$selected_post_type = get_option( 'obj_post_type' );

		add_action( 'add_meta_boxes_' . $selected_post_type, array( $this, 'setup_metabox' ), 10, 1 );

	}

	/**
	 * Set up the metabox
	 *
	 * @since 1.0
	 */
	public function setup_metabox( $post ) {

		add_meta_box( 'google-address-fields', __( 'Google Map Address', 'obj-google-maps' ), array( $this, 'metabox_content' ), $post->post_type, 'normal', 'high' );

	}

	/**
	 * Metabox markup
	 *
	 * @since 1.0
	 */
	public function metabox_content() {
		$html = '';
		$html .= 'testing';

		return $html;
	}

}
