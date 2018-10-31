<?php
/*
*/

defined('ABSPATH') or die('Blank Space');

// constant for plugin location
define('BOK_PLUGIN_URL', plugin_dir_url(__FILE__));

require_once 'inc/bok-posttype.php';
require_once 'inc/bok-shortcode.php';
require_once 'inc/bok-doc.php';


final class EM_bok {
	/* singleton */
	private static $instance = null;

	public static function get_instance() {
		if (self::$instance === null) self::$instance = new self();

		return self::$instance;
	}

	private function __construct() {
		
		Bok_posttype::get_instance();
		Bok_shortcode::get_instance();
		Bok_doc::get_instance();
	}

}