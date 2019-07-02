<?php
/*
*/

defined('ABSPATH') or die('Blank Space');

// constant for plugin location
define('LAN_DK_PLUGIN_URL', plugin_dir_url(__FILE__));
define('EMLANDK', 'emlanlistdk');

require_once 'inc/lan-dk-posttype.php';
require_once 'inc/lan-dk-shortcode.php';
require_once 'inc/lan-dk-doc.php';
require_once 'inc/lan-dk-links.php';


final class EM_dk_lan {
	/* singleton */
	private static $instance = null;

	public static function get_instance() {
		if (self::$instance === null) self::$instance = new self();

		return self::$instance;
	}

	private function __construct() {
		
		Lan_dk_posttype::get_instance();
		Lan_dk_shortcode::get_instance();
		Lan_dk_doc::get_instance();
		Lan_dk_links::get_instance();
	}

}