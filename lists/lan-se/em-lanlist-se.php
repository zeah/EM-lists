<?php
/*
*/

defined('ABSPATH') or die('Blank Space');

// constant for plugin location
define('LANLIST_SE_PLUGIN_URL', plugin_dir_url(__FILE__));

require_once 'inc/lanlist-posttype.php';
require_once 'inc/lanlist-shortcode.php';


final class EM_lanlist {
	/* singleton */
	private static $instance = null;

	public static function get_instance() {
		if (self::$instance === null) self::$instance = new self();

		return self::$instance;
	}

	private function __construct() {
		
		Lanlist_posttype::get_instance();
		Lanlist_shortcode::get_instance();
	}

}
// function init_emlanlistse() {
// 	Lanlist_posttype::get_instance();
// 	Lanlist_shortcode::get_instance();
// }
// add_action('plugins_loaded', 'init_emlanlistse');