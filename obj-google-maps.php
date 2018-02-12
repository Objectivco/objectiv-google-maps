<?php
/*
Plugin Name: Objectiv Google Maps
Plugin URI:  http://objectiv.co
Description: Create searchable Google maps
Version:     2.0
Author:      Objectiv, Matthew Sigley
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

// Prevent direct access
defined( 'WPINC' ) || header( 'HTTP/1.1 403' ) & exit;

class Obj_Gmaps {

	function __construct () {
    require_once 'includes/class-obj-admin.php';
    require_once 'includes/class-obj-settings.php';
    require_once 'includes/class-obj-public.php';
    require_once 'includes/obj-functions.php';

		$this->version = 2.0;
    $this->obj_admin = new Obj_Gmaps_Admin( __FILE__ );
    $this->obj_settings = new Obj_Gmaps_Settings( __FILE__, $version );
    $this->obj_public = new Obj_Gmaps_Public( __FILE__, $version );
	}
}

$Obj_Gmaps = new Obj_Gmaps();

