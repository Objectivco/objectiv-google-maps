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

    public function __construct( $file, $version ) {

		$this->file = $file;
		$this->version = $version;

        if ( is_admin() ) {
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
			<div class="obj-settings-page">
				<main class="obj-settings-page__content">
					Content
				</main>
				<aside class="obj-settings-page__sidebar">
					<div class="obj-settings-page__sidebar-box">
						<div class="obj-settings-page__box-wrap">
							<a href="http://objectiv.co" target="_blank"><img src="<?php echo plugins_url( '/assets/images/objectiv-logo.png', $this->file ); ?>" /></a>
							<h3>Google Maps Plugin</h3>
							<p>Create searchable post types on a Google Map.</p>
							<p><strong>Version:</strong> <?php echo $this->version; ?></p>
							<h3>Resources</h3>
							<ul>
								<li>
									<a href="#">Help</a>
								</li>
								<li>
									<a href="#">Knowledge Base</a>
								</li>
							</ul>
						</div>
					</div>
				</aside>
			</div>
        </div>
        <?php
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

}
