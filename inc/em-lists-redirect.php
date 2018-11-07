<?php
defined('ABSPATH') or die('Blank Space');

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


	/**
	 * redirecting 
	 */
	public function redirect() {

		// $url = $_SERVER['REQUEST_URI'];
		// wp_die('<xmp>'.print_r($_SERVER['REQUEST_URI'], true).'</xmp>');
		
		$lists = get_option('em_lists');
		$arr = [];
		foreach ($lists as $key => $value) {
			$temp = get_option($key.'_redirect');
			if (is_array($temp)) $arr += $temp;
		}

		if (!is_array($arr)) return;
		// wp_die('<xmp>'.print_r($arr, true).'</xmp>');
		foreach ($arr as $key => $a) {
			if (!is_array($a)) continue;

			if (!isset($a['pf'])) continue;

			if (!isset($a['url']) || !esc_url($a['url'])) continue;
			// avoiding infinite redirs
			// wp_die('<xmp>'.print_r($a['url'], true).'</xmp>');
			if (preg_match('/'.str_replace('/', '\/', $a['url']).'((\?.*)|$)/', $_SERVER['REQUEST_URI'])) continue;


			$url = str_replace('/', '\/', $key.$a['pf']);


			if (preg_match('/'.$url.'((\/?\?.*)|$)/', $_SERVER['REQUEST_URI'])) {
				if (strpos($a['url'], '?') === false && $_SERVER['QUERY_STRING']) $a['url'] .= '?';

				$a['url'] = rtrim($a['url'], '&');
				if ($_SERVER['QUERY_STRING']) $a['url'] .= '&';

				wp_redirect($a['url'].$_SERVER['QUERY_STRING']);
				exit;
			}
		}

		// wp_die('<xmp>'.print_r($arr, true).'</xmp>');
		// foreach($arr as $key => $url) {
		// 	// avoiding infinite loops
		// 	if (preg_match('/'.$key.'$/', $url)) continue;

		// 	if (preg_match('/'.$key.'((\?.*)|$)/', $_SERVER['REQUEST_URI'])) {

		// 		if (strpos($url, '?') === false && $_SERVER['QUERY_STRING']) $url .= '?';

		// 		wp_redirect($url.$_SERVER['QUERY_STRING']);
		// 		exit;
		// 	}
		// }
	}
}