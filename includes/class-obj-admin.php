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
		$this->dir = dirname( $file );

        // Activation and Deactivation Hooks
        register_activation_hook( $file, array( $this, 'activate_plugin' ) );
		register_deactivation_hook( $file, array( $this, 'deactivate_plugin' ) );

		if ( is_admin() ) {
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_js' ) );
			add_action( 'admin_init', array( $this, 'metaboxes_setup' ) );
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
	 * Register address metabox
	 *
	 * @since 1.0
	 */
	public function metaboxes_setup () {
		$selected_post_type = get_option( 'obj_post_type' );
		add_action( 'add_meta_boxes_' . $selected_post_type, array( $this, 'create_metabox' ), 10, 1 );
		add_action( 'save_post', array( $this, 'save_metabox' ), 10, 2 );
	}

	/**
	 * Create metabox
	 *
	 * @since 1.0
	 */
	public function create_metabox( $post ) {
		add_meta_box(
			'obj-google-address',
			__( 'Google Maps Address', 'obj-google-maps' ),
			array( $this, 'metabox_content' ),
			$post->post_type,
			'normal',
			'high'
		);
	}

	/**
	 * Create metabox content
	 *
	 * @since 1.0
	 */
	public function metabox_content( $object ) {
		wp_nonce_field( plugin_basename( $this->dir ), 'obj_google_address_nonce' );
		$data = array();
		?>
		<p>
			<label for="obj-google-address"><?php _e( "Add the address.", 'obj-google-maps' ); ?></label>
			<br />
			<input class="widefat" type="text" name="obj-google-address" id="obj-google-address" value="<?php echo esc_attr( get_post_meta( $object->ID, 'obj_google_address', true ) ); ?>" size="30" />
		</p>
		<?php

	}

	/**
	 * Save Metaboxes
	 *
	 * @since 1.0
	 */
	public function save_metabox( $post_id, $post ) {

		$selected_post_type = get_option( 'obj_post_type' );

		if ( ! isset( $_POST['obj_google_address_nonce'] ) || ! wp_verify_nonce( $_POST['obj_google_address_nonce'], plugin_basename( $this->dir ) ) ) {
			print 'Sorry, your nonce did not verify.';
			exit;
		}

		$new_meta_value = ( isset( $_POST['obj-google-address'] ) ? sanitize_html_class( $_POST['obj-google-address'] ) : '' );
		$meta_key = 'obj_google_address';
		$meta_value = get_post_meta( $post_id, $meta_key, true );
		update_post_meta( $post_id, $meta_key, $new_meta_value );
	}

}
