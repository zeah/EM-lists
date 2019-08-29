<?php 

final class Bok_ga {
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
		// add_action('wp_ajax_nopriv_ga', [$this, 'ga']);
		// add_action('wp_ajax_ga', [$this, 'ga']);
		add_action('init', [$this, 'ga']);
	}

	public function ga() {

		if ($_GET['action'] != 'ga') return;
		// exit;

		if ($_GET['list'] != 'bok'
			|| !isset($_GET['name'])
			|| !isset($_GET['site'])
			|| !isset($_GET['value'])
			|| !isset($_GET['id'])) return;

		EM_list_parts::ga_event($_GET['name'], $_GET['site'], $_GET['value'], $_GET['id']);
		exit;
	}
}