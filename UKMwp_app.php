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

    /**
     * Initier modulen
     */
    public static function init($pl_id = null) {
        parent::init( get_option('pl_id') );
        parent::setPluginPath( __DIR__ );
        
        //self::setAction('home');
        self::hook();
    }

    public static function hook() {
        self::require( 'api.class.php' );
        add_action( 'rest_api_init', ['UKMwpAPI', 'registerEndpoints'] );
    }
}

UKMappWP::init( get_option('pl_id') );
UKMappWP::hook();
