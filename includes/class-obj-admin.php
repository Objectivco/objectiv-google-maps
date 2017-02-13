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
			add_action( 'save_post', array( $this, 'save_location_lat_long' ), 10, 3 );
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
		$screen = get_current_screen();
		$selected_post_type = get_option( 'obj_post_type' );

		$data_array = array(
			'api_key'	=> get_option( 'obj_api_key' )
		);

		if ( $hook == 'settings_page_obj_google_map_settings' || $screen->post_type == $selected_post_type ) {
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
			<label for="autocomplete"><?php _e( "Add the address.", 'obj-google-maps' ); ?></label>
			<br />
			<input class="widefat" type="text" name="obj-google-address" id="autocomplete" value="<?php echo esc_attr( get_post_meta( $object->ID, 'obj_google_address', true ) ); ?>" size="30" />
		</p>
		<?php

	}

	function save_location_lat_long( $post_id, $post, $update ) {

	    $post_type = get_post_type($post_id);

	    // If this isn't a 'book' post, don't update it.
	    if ( get_option( 'obj_post_type' ) != $post_type ) return;

	    // - Update the post's metadata.
	    if ( isset( $_POST['obj-google-address'] ) ) {
	        $address = $_POST['obj-google-address'];
	        $string = str_replace (" ", "+", urlencode( $address ) );
	        $url = "http://maps.googleapis.com/maps/api/geocode/json?address=".$string."&sensor=false";

	        $response = wp_remote_get( $url );
	        $data = wp_remote_retrieve_body( $response );
	        $output = json_decode( $data );
	        if ($output->status == 'OK') {
				$address_components = $output->results[0]->address_components;
	            $geometry = $output->results[0]->geometry;
	            $longitude = $geometry->location->lng;
	            $latitude = $geometry->location->lat;
				update_post_meta( $post_id, 'obj_location_address_components', $address_components );
	            update_post_meta( $post_id, 'obj_location_lat', $latitude );
	            update_post_meta( $post_id, 'obj_location_lng', $longitude );
	        }
	    }
	}

	/**
	 * Save Metaboxes
	 *
	 * @since 1.0
	 */
	public function save_metabox( $post_id, $post ) {

		$selected_post_type = get_option( 'obj_post_type' );

		$new_meta_value = ( isset( $_POST['obj-google-address'] ) ? sanitize_text_field( $_POST['obj-google-address'] ) : '' );
		$new_place_id_value = ( isset( $_POST['obj-google-address-place-id'] ) ? sanitize_text_field( $_POST['obj-google-address-place-id'] ) : '' );
		$meta_key = 'obj_google_address';
		$meta_value = get_post_meta( $post_id, $meta_key, true );
		update_post_meta( $post_id, $meta_key, $new_meta_value );
	}

}
