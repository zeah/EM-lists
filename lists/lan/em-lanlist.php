<?php
/*
*/

defined('ABSPATH') or die('Blank Space');

// constant for plugin location
define('LANLIST_PLUGIN_URL', plugin_dir_url(__FILE__));
define('EMLAN', 'emlanlist');

require_once 'inc/lan-posttype.php';
require_once 'inc/lan-shortcode.php';
require_once 'inc/lan-doc.php';
require_once 'inc/lan-links.php';
require_once 'inc/lan-ga.php';


/* initiates plugin */
final class EM_lan {
	/* singleton */
	private static $instance = null;

	public static function get_instance() {
		if (self::$instance === null) self::$instance = new self();

		return self::$instance;
	}

	private function __construct() {
		Lan_posttype::get_instance();
		Lan_shortcode::get_instance();
		// Lan_doc::get_instance();
		Lan_links::get_instance();
		Lan_ga::get_instance();
	}
}
