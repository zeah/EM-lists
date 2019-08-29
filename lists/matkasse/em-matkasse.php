<?php
/*
*/

defined('ABSPATH') or die('Blank Space');

// constant for plugin location
define('MATKASSELIST_PLUGIN_URL', plugin_dir_url(__FILE__));
define('MAT', 'matkasselist');

require_once 'inc/matkasse-posttype.php';
require_once 'inc/matkasse-shortcode.php';
require_once 'inc/matkasse-doc.php';
require_once 'inc/matkasse-links.php';
require_once 'inc/matkasse-ga.php';


/* initiates plugin */
final class EM_matkasse {
	/* singleton */
	private static $instance = null;

	public static function get_instance() {
		if (self::$instance === null) self::$instance = new self();

		return self::$instance;
	}

	private function __construct() {
		Matkasse_posttype::get_instance();
		Matkasse_shortcode::get_instance();
		// Matkasse_doc::get_instance();
		Matkasse_links::get_instance();
		Matkasse_ga::get_instance();
	}
}
