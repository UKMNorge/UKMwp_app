<?php  
/* 
Plugin Name: UKMapp Wordpress-verktøy
Plugin URI: http://org.ukm.no
Description: Gir input til app'en på forskjellig vis forhåpentligvis etter hvert.
Author: UKM Norge / M Mandal & T Hovstad
Version: 0.1 
Author URI: http://org.ukm.no
*/

require_once('UKM/wp_modul.class.php');
class UKMappWP extends UKMWPmodul {
    public static $path_plugin = null;
    public static $action = null;

    public static function hook() {
        static::require( 'api.class.php' );
        add_action( 'rest_api_init', ['UKMwpAPI', 'registerEndpoints'] );
    }

    public static function meny(){
    }
}

UKMappWP::init( __DIR__ );
UKMappWP::hook();
