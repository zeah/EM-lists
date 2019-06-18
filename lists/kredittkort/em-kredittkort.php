<?php
/*
*/

defined('ABSPATH') or die('Blank Space');

// constant for plugin location
define('KREDITTKORT_PLUGIN_URL', plugin_dir_url(__FILE__));
define('KREDITTKORT', 'kredittkort');


require_once 'inc/kredittkort-posttype.php';
require_once 'inc/kredittkort-shortcode.php';

final class EM_kredittkort {
	/* singleton */
	private static $instance = null;

	public static function get_instance() {
		if (self::$instance === null) self::$instance = new self();

		return self::$instance;
	}

	private function __construct() {
		
		Kredittkort_posttype::get_instance();
		Kredittkort_shortcode::get_instance();
	}

}