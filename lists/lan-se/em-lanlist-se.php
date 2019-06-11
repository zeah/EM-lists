<?php
/*
*/

defined('ABSPATH') or die('Blank Space');

// constant for plugin location
define('LAN_SE_PLUGIN_URL', plugin_dir_url(__FILE__));
define('EMLAN_SE', 'emlanlistse');

require_once 'inc/lan-se-posttype.php';
require_once 'inc/lan-se-shortcode.php';
require_once 'inc/lan-se-doc.php';
require_once 'inc/lan-se-links.php';


final class EM_se_lan {
	/* singleton */
	private static $instance = null;

	public static function get_instance() {
		if (self::$instance === null) self::$instance = new self();

		return self::$instance;
	}

	private function __construct() {
		
		Lan_se_posttype::get_instance();
		Lan_se_shortcode::get_instance();
		Lan_se_doc::get_instance();
		Lan_se_links::get_instance();
	}

}