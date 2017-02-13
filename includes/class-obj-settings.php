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
	private $settings;
	private $settings_slug;
	private $settings_base;

    public function __construct( $file, $version ) {

		$this->file = $file;
		$this->version = $version;
		$this->settings_slug = 'obj_google_map_settings';
		$this->settings_base = 'obj_';

        if ( is_admin() ) {
			add_action( 'init', array( $this, 'load_settings' ), 11 );
			add_action( 'admin_init', array( $this, 'register_settings' ) );
            add_action( 'admin_menu', array( $this, 'add_settings_page' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_css' ) );
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
            __( 'Google Maps Settings', 'obj-google-maps' ),
            __( 'Google Maps', 'obj-google-maps' ),
            'manage_options',
            $this->settings_slug,
            array( $this, 'settings_page_markup' )
        );
    }

	/**
	 * Enqueue Admin CSS
	 *
	 * @since 1.0
	 */
	public function enqueue_admin_css() {
		$screen = get_current_screen();

		if ( $screen->id == 'settings_page_obj_google_map_settings' ) {
			wp_enqueue_style( 'obj-google-maps-admin-css', plugins_url( '/assets/css/admin/admin.css', $this->file ), array(), $this->version );
		}

	}

	/**
	 * Load Settings
	 *
	 * @since 1.0
	 */
	public function load_settings() {
		$this->settings = $this->settings_fields();
	}

	/**
	 * Build settings fields array
	 *
	 * @return array
	 * @since 1.0
	 */
	private function settings_fields() {

		global $wp_post_types;
		$post_type_options = array();
		$map_types = array();

		foreach( $wp_post_types as $post_type => $data ) {
			if ( in_array( $post_type, array( 'page', 'attachment', 'revision', 'nav_menu_item', 'wooframework', 'custom_css', 'customize_changeset', 'customize' ) ) ){
				continue;
			}
			$post_type_options[ $post_type ] = $data->labels->name;
		}

		$map_types = array(
			'roadmap'	=> 'Road Map',
			'satellite'	=> 'Satellite',
			'hybrid'	=> 'Hybrid',
			'terrain'	=> 'Terrain'
		);

		$zoom_options = array(
			1	=> 1,
			2	=> 2,
			3	=> 3,
			4	=> 4,
			5	=> 5,
			6	=> 6,
			7	=> 7,
			8	=> 8,
			9	=> 9,
			10	=> 10,
			11	=> 11,
			12	=> 12,
			13	=> 13,
			14	=> 14,
			15	=> 15,
			16	=> 16,
			17	=> 17,
			18	=> 18,
			19	=> 19,
			20	=> 20
		);

		$settings = array();

		$settings['general'] = array(
			'title'	=> __( '', 'obj-google-maps' ),
			'description'	=> __( '', 'obj-google-maps' ),
			'fields'	=> array(
				array(
					'id'	=> 'post_type',
					'label'	=> __( 'Post Type', 'obj-google-maps' ),
					'description'	=> __( 'Select the post type that you would like to display in the map.', 'obj-google-maps' ),
					'type'	=> 'select',
					'options'	=> $post_type_options,
					'default'	=> ''
				),
				array(
					'id'	=> 'map_type',
					'label'	=> __( 'Map Type', 'obj-google-maps' ),
					'description'	=> __( 'Select which type of map you would like to display', 'obj-google-maps' ),
					'type'	=> 'select',
					'options'	=> $map_types,
					'default'	=> 'roadmap'
				),
				array(
					'id'	=> 'map_height',
					'label'	=> __( 'Map Height', 'obj-google-maps' ),
					'description'	=> __( 'Define the height of the map in pixels.', 'obj-google-maps' ),
					'type'	=> 'text',
					'default'	=> '400px',
					'placeholder'	=> __( '400px', 'obj-google-maps' ),
					'class'	=> 'regular-text',
					'callback'	=> 'wp_strip_all_tags'
				),
				array(
					'id'	=> 'map_center',
					'label'	=> __( 'Map Center', 'obj-google-maps' ),
					'description'	=> __( 'Enter the address that you want to center the map on.', 'obj-google-maps' ),
					'type'	=> 'autocomplete',
					'placeholder'	=> __( 'Enter an address', 'obj-google-maps' ),
					'class'	=> 'regular-text',
					'callback'	=> 'wp_strip_all_tags'
				),
				array(
					'id'	=> 'map_zoom',
					'label'	=> __( 'Map Zoom', 'obj-google-maps' ),
					'description'	=> __( 'Select the zoom level for the map', 'obj-google-maps' ),
					'type'	=> 'select',
					'options'	=> $zoom_options,
					'default'	=> '8'
				),
				array(
					'id'	=> 'map_search_by',
					'label'	=> __( 'Search By', 'obj-google-maps' ),
					'description' => __( 'Select whether you want your visitors to be able to search for locations by address, city, or region.' ),
					'type'	=> 'select',
					'options'	=> array(
						'geocode'	=> 'Geocode',
						'address'	=> 'Address',
						'establishment'	=> 'Establishment',
						'(cities)'	=> 'City',
						'(regions)'	=> 'Region'
					),
					'default'	=> 'address'
				),
				array(
					'id'	=> 'api_key',
					'label'	=> __( 'Google API Key', 'obj-google-maps' ),
					'description'	=> __( 'Enter the Google API key to use this plugin.', 'obj-google-maps' ),
					'type'	=> 'text',
					'default'	=> '',
					'placeholder'	=> __( 'Google API Key', 'obj-google-maps' ),
					'class'	=> 'regular-text',
					'callback'	=> 'wp_strip_all_tags'
				)
			)
		);

		$settings = apply_filters( 'obj_settings_fields', $settings );

		return $settings;

	}

	/**
	 * Register Settings
	 *
	 * @since 1.0
	 */
	public function register_settings() {

		if ( is_array( $this->settings ) ) {

			// Loop through sections and run add_settings_section
			foreach( $this->settings as $section => $data ) {
				add_settings_section(
					$section,
					$data['title'],
					array( $this, 'settings_section' ),
					$this->settings_slug
				);

				// Loop through all of the fields and run add_settings_field
				foreach( $data['fields'] as $field ) {

					$option_name = $this->settings_base . $field['id'];

					$validation = '';
					if ( isset( $field['callback'] ) ) {
						$validation = $field['callback'];
					}

					register_setting( $this->settings_slug, $option_name, $validation );

					add_settings_field(
						$field['id'],
						$field['label'],
						array( $this, 'display_field' ),
						$this->settings_slug,
						$section,
						array(
							'field'	=> $field,
							'prefix'	=> $this->settings_base
						)
					);
				}
			}

		}

	}

	/**
	 * Settings Section
	 *
	 * @since 1.0
	 */
	public function settings_section( $section ) {
		$html = '';

		echo $html;
	}

	/**
	 * Generate HTML for displaying fields
	 *
	 * @since 1.0
	 */
	public function display_field( $args ) {
		$field = $args['field'];

		$html = '';

		$option_name = $this->settings_base . $field['id'];

		$default = '';
		if ( isset( $field['default'] ) ) {
			$default = $field['default'];
		}

		$data = get_option( $option_name, $default );

		$class = '';
		if ( isset( $field['class'] ) ) {
			$class = $field['class'];
		}

		switch ( $field['type'] ) {

			case 'text':
			case 'password':
			case 'number':
				$html .= '<input id="' . esc_attr( $field['id'] ) . '" type="' . $field['type'] . '" name="' . esc_attr( $option_name ) . '" placeholder="' . esc_attr( $field['placeholder'] ) . '" value="' . esc_attr( $data ) . '" class="' . $class . '" />' . "\n";
			break;

			case 'checkbox':
				$checked = '';
				if ( $data && 'on' == $data ){
					$checked = 'checked="checked"';
				}
				$html .= '<input id="' . esc_attr( $field['id'] ) . '" type="' . $field['type'] . '" name="' . esc_attr( $option_name ) . '" ' . $checked . ' class="' . $class . '"/>' . "\n";
			break;

			case 'autocomplete':
				$html .= '<input id="' . esc_attr( $field['type'] ) . '" type="text" name="' . esc_attr( $option_name ) . '" placeholder="' . esc_attr( $field['placeholder'] ) . '" value="' . esc_attr( $data ) . '" class="' . $class . '"/>' . "\n";
			break;

			case 'select':

				$html .= '<select name="' . esc_attr( $option_name ) . '" id="' . esc_attr( $field['id'] ) . '" class="' . $class . '">';
					foreach( $field['options'] as $key => $value ) {
						$selected = false;
						if ( $key == $data ) {
							$selected = true;
						}

						$html .= '<option ' . selected( $selected, true, false ) . 'value="' . esc_attr( $key ) . '">' . esc_html( $value ) . 	'</option>';
					}
				$html .= '</select>';
			break;

		}

		echo $html;
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
			<div class="obj-settings-page">
				<main class="obj-settings-page__content">
					<form method="post" action="options.php" enctype="multipart/form-data">
						<?php
						settings_fields( $this->settings_slug );
						do_settings_sections( $this->settings_slug );
						submit_button();
						?>
					</form>
				</main>
				<aside class="obj-settings-page__sidebar">
					<div class="obj-settings-page__sidebar-box">
						<div class="obj-settings-page__box-wrap">
							<a href="http://objectiv.co" target="_blank"><img src="<?php echo plugins_url( '/assets/images/objectiv-logo.png', $this->file ); ?>" /></a>
							<h3>Google Maps Plugin</h3>
							<p>Create searchable post types on a Google Map.</p>
							<p><strong>Version:</strong> <?php echo $this->version; ?></p>
							<!-- <h3>Resources</h3>
							<ul>
								<li>
									<a href="#">Help</a>
								</li>
								<li>
									<a href="#">Knowledge Base</a>
								</li>
							</ul> -->
						</div>
					</div>
					<div class="obj-settings-page__sidebar-box">
						<div class="obj-settings-page__box-wrap">
							<h3>Displaying The Map</h3>
							<p>Use the shortcode below to display the map on any page or post.</p>
							<p><code>[objectiv_google_maps]</code></p>
						</div>
					</div>
				</aside>
			</div>
        </div>
        <?php
    }

}
