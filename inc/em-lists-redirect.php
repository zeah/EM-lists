<?php

final class EM_list_redirect {
	/* singleton */
	private static $instance = null;

	public static function get_instance() {
		if (self::$instance === null) self::$instance = new self();

		return self::$instance;
	}

	private function __construct() {
		$this->wp_hooks();
	}

	private function wp_hooks() {
		add_action('init', array($this, 'redirect'));
	}

	public function redirect() {

		$url = $_SERVER['REQUEST_URI'];
		// wp_die('<xmp>'.print_r($_SERVER, true).'</xmp>');
		// wp_die('<xmp>'.print_r(get_home_url(), true).'</xmp>');
		
		// wp_die('<xmp>'.print_r($url, true).'</xmp>');
		$opt = get_option('emlanlist_redirect');
		// wp_die('<xmp>'.print_r($opt, true).'</xmp>');
		// if ()
		foreach($opt as $key => $val)
			if (strpos($url, $key) !== false) {
				if (strpos($val, '?') === false) $val .= '?';

				// wp_die('<xmp>'.print_r($val.$_SERVER['QUERY_STRING'], true).'</xmp>');
				wp_redirect($val.$_SERVER['QUERY_STRING']);
				exit;
			}
			// wp_die('<xmp>'.print_r(strpos($url, $key), true).'</xmp>');
				
			// if (preg_match('/^'..'/', $key))
		// wp_die('<xmp>'.print_r(get_option('emlanlist_redirect'), true).'</xmp>');
		
		
	}
}