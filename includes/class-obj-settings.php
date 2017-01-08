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

		foreach( $wp_post_types as $post_type => $data ) {
			if ( in_array( $post_type, array( 'page', 'attachment', 'revision', 'nav_menu_item', 'wooframework', 'custom_css', 'customize_changeset', 'customize' ) ) ){
				continue;
			}
			$post_type_options[ $post_type ] = $data->labels->name;
		}

		$settings = array();

		$settings['general'] = array(
			'title'	=> __( 'General', 'obj-google-maps' ),
			'description'	=> __( '', 'obj-google-maps' ),
			'fields'	=> array(
				array(
					'id'	=> 'post_type',
					'label'	=> __( 'Post Type', 'obj-google-maps' ),
					'description'	=> __( 'Select the post type that you would like to display in the map.', 'obj-google-maps' ),
					'type'	=> 'select',
					'options'	=> $post_type_options,
					'default'	=> ''
				)
			)
		);

		$settings['settings'] = array(
			'title'	=> __( 'Settings', 'obj-google-maps' ),
			'description' => __( '', 'obj-google-maps' ),
			'fields'	=> array(
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
		$html = '<p>Section</p>';

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

			case 'select':
				$selected = false;
				if ( $key == $data ) {
					$selected = true;
				}

				$html .= '<select name="' . esc_attr( $option_name ) . '" id="' . esc_attr( $field['id'] ) . '" class="' . $class . '">';
					foreach( $field['options'] as $key => $value ) {
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

		$tabs = '';
		$tab = 'general';
		if ( isset( $_GET['tab'] ) && $_GET['tab'] ) {
			$tab = $_GET['tab'];
		}

		if ( is_array( $this->settings ) ) {
			$tabs .= '<h2 class="nav-tab-wrapper">' . "\n";
			foreach ( $this->settings as $section => $data ) {
				// Set tab class
				$class = 'nav-tab';
				if ( ! isset( $_GET['tab'] ) ) {
					if ( 0 == $c ) {
						$class .= ' nav-tab-active';
					}
				} else {
					if ( isset( $_GET['tab'] ) && $section == $_GET['tab'] ) {
						$class .= ' nav-tab-active';
					}
				}
				// Set tab link
				$tab_link = add_query_arg( array( 'tab' => $section ) );
				if ( isset( $_GET['settings-updated'] ) ) {
					$tab_link = remove_query_arg( 'settings-updated', $tab_link );
				}
				if ( isset( $_GET['feed-series'] ) ) {
					$tab_link = remove_query_arg( 'feed-series', $tab_link );
				}
				// Output tab
				$tabs .= '<a href="' . esc_url( $tab_link ) . '" class="' . esc_attr( $class ) . '">' . esc_html( $data['title'] ) . '</a>' . "\n";
				++$c;
			}
			$tabs .= '</h2>' . "\n";
		}

		?>
        <div class="wrap">
            <h2><?php echo esc_html( get_admin_page_title() ); ?></h2>
			<div class="obj-settings-page">
				<main class="obj-settings-page__content">
					<?php echo $tabs; ?>
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
				</aside>
			</div>
        </div>
        <?php
    }

}
