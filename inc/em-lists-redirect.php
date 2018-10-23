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
		
		$opt = get_option('emlanlist_redirect');
		if (!is_array($opt)) return;

		foreach($opt as $key => $url) {
			// avoiding infinite loops
			if (preg_match('/'.$key.'$/', $url)) continue;

			if (preg_match('/'.$key.'((\?.*)|$)/', $_SERVER['REQUEST_URI'])) {
				if (strpos($url, '?') === false && $_SERVER['QUERY_STRING']) $url .= '?';

				wp_redirect($url.$_SERVER['QUERY_STRING']);
				exit;
			}
		}
	}
}