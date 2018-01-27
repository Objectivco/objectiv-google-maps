<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Public Views class
 *
 * @author      Wes Cole
 * @category    Class
 * @package     ObjectivGoogleMaps/Classes
 * @since       1.0
 */
class Obj_Gmaps_Public {

    public function __construct( $file, $version ) {

        $this->file = $file;
        $this->version = $version;

        add_action( 'init', array( $this, 'add_map_shortcode' ) );
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_js' ) );

    }

	/**
     * Enqueue JS
     *
     * @since 1.0
     */
    public function enqueue_js() {

        if ( obj_has_shortcode( 'objectiv_google_maps' ) ) {
            wp_enqueue_script( 'obj-google-maps', plugins_url( '/assets/js/build/main.js', $this->file ), array(), $this->version, true );
			wp_enqueue_style( 'obj-google-maps-style', plugins_url( '/assets/css/public/public.css', $this->file ), array(), $this->version );
        }

    }

    /**
     * Add map shortcode
     *
     * @since 1.0
     */
    public function add_map_shortcode() {

        add_shortcode( 'objectiv_google_maps', array( $this, 'map_shortcode_markup' ) );

    }

    /**
     * Create Map Shortcode Markup
     *
     * @since 1.0
     */
    public function map_shortcode_markup() {
		$selected_post_type = get_option( 'obj_post_type' );
		$height = get_option( 'obj_map_height' );
		$search_by = get_option( 'obj_map_search_by' );
		$placeholder = '';

		$posts_arg = array(
			'post_type'	=> $selected_post_type,
			'posts_per_page'	=> -1
		);

		$post_type_object = get_post_type_object( $selected_post_type );
		$post_type_object_labels = $post_type_object->labels;

		$posts = get_posts( $posts_arg );

		foreach( $posts as $key => $post ) {
			$lat = get_post_meta( $post->ID, 'obj_location_lat', true );
			$lng = get_post_meta( $post->ID, 'obj_location_lng', true );

			if ( $lat && $lng ) {
				$posts[$key]->lat = $lat;
				$posts[$key]->lng = $lng;
				$posts[$key]->permalink = get_the_permalink( $post->ID );
				$posts[$key]->post_type_label = $post_type_object_labels->singular_name;
				$posts[$key]->address = get_post_meta( $post->ID, 'obj_google_address', true );
				$posts[$key]->address_components = get_post_meta( $post->ID, 'obj_location_address_components', true );
			}
		}

		$data_array = array(
			'apiKey'	=> get_option( 'obj_api_key' ),
			'mapType'	=> get_option( 'obj_map_type' ),
			'mapCenter'	=> get_option( 'obj_map_center' ),
			'mapCenterAddressCompnents' => wp_cache_get( 'obj_map_center_address_components' ),
			'mapCenterLat' => wp_cache_get( 'obj_map_center_lat' ),
			'mapCenterLng' => wp_cache_get( 'obj_map_center_lng' ),
			'mapZoom'	=> get_option( 'obj_map_zoom' ),
			'mapSearch'	=> get_option( 'obj_map_search_by' ),
			'mapLocationIcon'	=> get_option( 'obj_map_location_icon' ),
			'locations'	=> $posts
		);

		if( !empty( $data_array['mapCenter'] ) 
			&& ( empty( $data_array['mapCenterAddressCompnents'] ) || empty( $data_array['mapCenterLat'] ) || empty( $data_array['mapCenterLng'] ) ) ) {
			$this->update_center_lat_long_cache( $data_array );
		}

		if ( obj_has_shortcode( 'objectiv_google_maps' ) ) {
			wp_localize_script( 'obj-google-maps', 'data', $data_array );
		}

		if ( $search_by == 'geocode' ) {
			$placeholder = 'Search...';
		} else if ( $search_by == 'address' ) {
			$placeholder = 'Search by Address...';
		} else if ( $search_by == 'establishment' ) {
			$placeholder = 'Search by Establishment...';
		} else if ( $search_by == '(cities)' ) {
			$placeholder = 'Search by City...';
		} else if ( $search_by == '(regions)' ) {
			$placeholder = 'Search by Regions...';
		}


        ob_start();

		echo '<div id="obj-google-map-wrap">';
		echo '<input id="obj-search-input" class="controls" type="text" placeholder="' . $placeholder . '">';
        echo '<div id="obj-google-maps" style="height:' . $height . ';"></div>';
		echo '</div>';

        return ob_get_clean();

    }

	private function update_center_lat_long_cache( &$data_array ) {
		$string = str_replace (" ", "+", urlencode( $data_array['mapCenter'] ) );
		$url = "http://maps.googleapis.com/maps/api/geocode/json?address=".$string."&sensor=false";

		$response = wp_remote_get( $url );
		$data = wp_remote_retrieve_body( $response );
		$output = json_decode( $data );
		if (!empty($output) && $output->status == 'OK') {
			$address_components = $output->results[0]->address_components;
			$geometry = $output->results[0]->geometry;
			$longitude = $geometry->location->lng;
			$latitude = $geometry->location->lat;
			wp_cache_set( 'obj_map_center_address_components', $address_components );
			wp_cache_set( 'obj_map_center_lat', $latitude );
			wp_cache_set( 'obj_map_center_lng', $longitude );
			$data_array['mapCenterAddressCompnents'] = $address_components;
			$data_array['mapCenterLat'] = $latitude;
			$data_array['mapCenterLng'] = $longitude;
		}
	}
}
