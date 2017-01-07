<?php
/*
Plugin Name: Objectiv Google Maps
Plugin URI:  http://objectiv.co
Description: Create searchable Google maps
Version:     1.0
Author:      Objectiv
Author URI:  http://objectiv.co
License:     GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: obj-google-maps
Domain Path: /languages
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once( 'includes/class-obj-admin.php' );
require_once( 'includes/class-obj-settings.php' );

global $obj_admin, $obj_settings;
$obj_admin = new Obj_Gmaps_Admin( __FILE__ );
$obj_settings = new Obj_Gmaps_Settings();
