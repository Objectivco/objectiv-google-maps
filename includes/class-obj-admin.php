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
			require_once 'class-obj-uibuilder.php';
			require_once 'class-obj-data-validator.php';

			$this->file = $file;
			$this->dir = dirname( $file );
			$this->uibuilder = new Obj_Gmaps_UIBuilder( 'obj_location' );
			$this->datavalidator = new Obj_Gmaps_DataValidator();
			$this->maps_api_key = get_option( 'obj_maps_api_key' );
			$this->geocode_api_key = get_option( 'obj_geocode_api_key' );

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
		$screen = get_current_screen();
		$selected_post_type = get_option( 'obj_post_type' );

		$data_array = array(
			'api_key'	=> $this->api_key
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
		if( !empty($selected_post_type) ) {
			add_action( 'add_meta_boxes_'.$selected_post_type, array( $this, 'create_metabox' ), 10, 1 );
			add_action( 'save_post_'.$selected_post_type, array( $this, 'save_post_validate' ), 9, 2 );
			add_action( 'save_post_'.$selected_post_type, array( $this, 'verify_wp_nonces' ), 10, 2 );
			add_action( 'save_post_'.$selected_post_type, array( $this, 'save_metabox' ), 11, 1 );
		}
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
		wp_nonce_field( 'obj_google_save', 'obj_google_save_nonce_'.$object->ID );
		
		$lat = get_post_meta( $object->ID, 'obj_location_lat', true );
		if( empty($lat) )
			$lat = 'Not set. Marker will not appear on map. Save the post to try geocoding the address again.';
		$lng = get_post_meta( $object->ID, 'obj_location_lng', true );
		if( empty($lng) )
			$lng = 'Not set. Marker will not appear on map. Save the post to try geocoding the address again.';
		?>
		<table class="form-table">
			<tbody>
				<tr valign="top">
					<th scope="row">
						<label for="autocomplete"><?php _e( "Address", 'obj-google-maps' ); ?></label>
					</th>
					<td>
						<input class="widefat" type="text" name="obj-google-address" id="autocomplete" value="<?php echo esc_attr( get_post_meta( $object->ID, 'obj_google_address', true ) ); ?>" size="30" />
					</td>
				</tr>
				<tr valign="top">
					<th scope="row">
						<?php _e( "Address Latitude", 'obj-google-maps' ); ?>
					</th>
					<td>
						<?php echo $lat; ?>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row">
						<?php _e( "Address Longitude", 'obj-google-maps' ); ?>
					</th>
					<td>
						<?php echo $lng; ?>
					</td>
				</tr>
				<?php
				$custom_post_meta = array();
				$custom_post_meta = apply_filters( 'obj_location_post_meta', $custom_post_meta );
				// Supported field types: date, time, textbox, url, email, hidden, tel, textarea
				// TODO: Add support for checkbox, number, and selectbox field types. UI functions exist but the value logic below will not work for them.
				foreach( $custom_post_meta as $meta_key => $field_array ) {
					if( empty($field_array['type']) || empty($field_array['label']) 
						|| !is_callable( array($this->uibuilder, $field_array['type']) ) )
						continue;

					$meta_value = get_post_meta( $object->ID, $this->uibuilder->get_name_id($meta_key), true );
					?>
					<tr valign="top">
						<th scope="row">
							<label for="<?php echo $this->uibuilder->get_name_id($meta_key); ?>"><?php _e( $field_array['label'], 'obj-google-maps' ); ?></label>
						</th>
						<td>
							<?php echo $this->uibuilder->{$field_array['type']}( $meta_key, $meta_value, 'widefat' ); ?>
						</td>
					</tr>
					<?php
				}
				?>
			</tbody>
		</table>
		<?php
	}

	public function save_post_validate( $post_id, $post ) {
		if( !$this->is_valid_post_save($post) ) {
			//Remove post saving actions
			$post_type = $post->post_type;
			remove_action( 'save_post_'.$post_type, array( $this, 'verify_wp_nonces' ), 10 );
			remove_action( 'save_post_'.$post_type, array( $this, 'save_metabox' ), 11 );
		}
	}

	private function is_valid_post_save($post) {
		//Check for auto saves, creating a new post, no post array, and unhandled post types
		if( is_array($post) )
			$post = (object) $post;
		if( empty($_POST) 
			|| 'auto-draft' == $post->post_status
			|| 'trash' == $post->post_status
			|| (defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE) )
			return false;
		return true;
	}

	public function verify_wp_nonces( $post_id, $post ) {
		if( !$this->is_valid_post_save($post) )
			return; //Do nothing
		
		if( !isset( $_POST['obj_google_save_nonce_'.$post_id] ) )
			$this->display_error('verify_nonce', 'Unable to verify security nonce.');
		
		check_admin_referer( 'obj_google_save', 'obj_google_save_nonce_'.$post_id );
	}

	public function save_location_lat_long( $post_id, $address ) {
		// - Update the post's metadata.
		if ( !empty($address) ) {
			$string = str_replace (" ", "+", urlencode( $address ) );
			$url = "https://maps.googleapis.com/maps/api/geocode/json?address=".$string;
			if( !empty( $this->geocode_api_key) )
				$url .= '&key='.urlencode($this->geocode_api_key);

			$response = wp_remote_get( $url );
			$data = wp_remote_retrieve_body( $response );
			$output = json_decode( $data );

			if ( !empty($output) && $output->status == 'OK' ) {
				$address_components = $output->results[0]->address_components;
				$geometry = $output->results[0]->geometry;
				$latitude = $geometry->location->lat;
				$longitude = $geometry->location->lng;
				update_post_meta( $post_id, 'obj_location_address_components', $address_components );
				update_post_meta( $post_id, 'obj_location_lat', $latitude );
				update_post_meta( $post_id, 'obj_location_lng', $longitude );
				return true;
			}
		}

		delete_post_meta( $post_id, 'obj_location_address_components' );
		delete_post_meta( $post_id, 'obj_location_lat' );
		delete_post_meta( $post_id, 'obj_location_lng' );
		return false;
	}

	/**
	 * Save Metaboxes
	 *
	 * @since 1.0
	 */
	public function save_metabox( $post_id ) {
		//Save location address
		$new_address = '';
		$address = get_post_meta( $post_id, 'obj_google_address', true );
		$address_components = get_post_meta( $post_id, 'obj_location_address_components', true );
		$latitude = get_post_meta( $post_id, 'obj_location_lat', true );
		$longitude = get_post_meta( $post_id, 'obj_location_lng', true );
		if( isset( $_POST['obj-google-address'] ) )
			$new_address = sanitize_text_field( $_POST['obj-google-address'] );
		//Prevent resaves from regeocoding address if lat and lng already exist
		if( $new_address != $address || empty( $address_components ) || empty( $latitude ) || empty( $longitude ) ) {
			update_post_meta( $post_id, 'obj_google_address', $new_address );
			$this->save_location_lat_long( $post_id, $new_address );
		}

		//Save location post meta
		$custom_post_meta = array();
		$custom_post_meta = apply_filters( 'obj_location_post_meta', $custom_post_meta );
		// Supported field types: date, time, textbox, url, email, hidden, tel, textarea
		// TODO: Add support for checkbox, number, and selectbox field types. UI functions exist but the saving logic below will not work for them.
		$errors = array();
		foreach( $custom_post_meta as $meta_key => $field_array ) {
			$meta_value = '';
			if( isset( $_POST[$this->uibuilder->get_name_id($meta_key)] ) ) {
				$meta_value = trim( $_POST[$this->uibuilder->get_name_id($meta_key)] );
				if( !empty( $meta_value ) ) {
					switch( $field_array['type'] ) {
						case 'textbox':
						case 'hidden':
						case 'tel':
							$meta_value = sanitize_text_field( $meta_value );
							break;
						case 'date':
							if( !$this->datavalidator->validate_date( $meta_value ) ) {
								$meta_value = '';
								$errors[] = $field_array['label'] . ': Please enter a valid date in YYYY-MM-DD format.';
							}
							break;
						case 'time':
							if( !$this->datavalidator->validate_time( $meta_value ) ) {
								$meta_value = '';
								$errors[] = $field_array['label'] . ': Please enter a valid time in HH:MM format.';
							}
							break;
						case 'url':
							$meta_value = $this->datavalidator->sanitize_url( $meta_value, array( 'http', 'https' ) );
							break;
						case 'email':
							if( !$this->datavalidator->validate_email( $meta_value ) ) {
								$meta_value = '';
								$errors[] = $field_array['label'] . ': Please enter a valid email address.';
							}
							break;
						case 'textarea':
							$meta_value = wp_filter_kses( $meta_value );
							break;
					}
				}
			}
			update_post_meta( $post_id, $this->uibuilder->get_name_id($meta_key), $meta_value );
		}

		if( !empty( $errors ) ) {
			$errors = "<br />\n" . implode( "<br />\n", $errors );
			$this->display_error('location_meta', $errors);
		}
	}

	private function display_error($code, $message) {
		wp_die( new WP_Error('obj_google_'.$code, 'Objectiv Google Maps: '.$message) );
	}
}
