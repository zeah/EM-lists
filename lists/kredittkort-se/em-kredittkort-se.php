<?php
/*
*/

defined('ABSPATH') or die('Blank Space');

// constant for plugin location
define('KREDITTKORT_SE_PLUGIN_URL', plugin_dir_url(__FILE__));
define('KREDITTKORT_SE', 'emkredittkortse');

require_once 'inc/kredittkort-se-posttype.php';
require_once 'inc/kredittkort-se-shortcode.php';
require_once 'inc/kredittkort-se-doc.php';
require_once 'inc/kredittkort-se-links.php';


final class EM_kredittkort_se {
	/* singleton */
	private static $instance = null;

	public static function get_instance() {
		if (self::$instance === null) self::$instance = new self();

		return self::$instance;
	}

	private function __construct() {
		
		Kredittkort_se_posttype::get_instance();
		Kredittkort_se_shortcode::get_instance();
		Kredittkort_se_doc::get_instance();
		Kredittkort_se_links::get_instance();
	}

}