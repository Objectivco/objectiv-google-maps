<?php
/*
Plugin Name: Objectiv Google Maps
Plugin URI:  http://objectiv.co
Description: Create searchable Google maps
Version:     1.1
Author:      Objectiv
Author URI:  http://objectiv.co
License:     GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: obj-google-maps

Objectiv Google Maps is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 2 of the License, or
any later version.

Objectiv Google Maps is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with Objectiv Google Maps. If not, see https://www.gnu.org/licenses/gpl-2.0.html.
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$version = 1.1;

require_once( 'includes/class-obj-admin.php' );
require_once( 'includes/class-obj-settings.php' );
require_once( 'includes/class-obj-public.php' );
require_once( 'includes/obj-functions.php' );

global $obj_admin, $obj_settings, $obj_public;
$obj_admin = new Obj_Gmaps_Admin( __FILE__ );
$obj_settings = new Obj_Gmaps_Settings( __FILE__, $version );
$obj_public = new Obj_Gmaps_Public( __FILE__, $version );
