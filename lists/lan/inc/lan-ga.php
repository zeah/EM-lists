<?php 

final class Lan_ga {
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
		add_action('init', [$this, 'ga']);
	}

	public function ga() {

		if (!isset($_GET['action']) || $_GET['action'] != 'ga') return;

		if (!isset($_GET['list'])
			|| $_GET['list'] != 'lan'
			|| !isset($_GET['name'])
			|| !isset($_GET['site'])
			|| !isset($_GET['value'])
			|| !isset($_GET['id'])) return;

		EM_list_parts::ga_event($_GET['name'], $_GET['site'], $_GET['value'], $_GET['id']);
		exit;
	}
}