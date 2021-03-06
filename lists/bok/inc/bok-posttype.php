<?php 
defined('ABSPATH') or die('Blank Space');

require_once 'bok-taxonomy.php';
require_once 'bok-edit.php';
require_once 'bok-overview.php';

/**
 * 
 */
final class Bok_posttype {
	/* singleton */
	private static $instance = null;

	public static function get_instance() {
		if (self::$instance === null) self::$instance = new self();
		return self::$instance;
	}

	private function __construct() {
		Bok_edit::get_instance();
		Bok_taxonomy::get_instance();
		Bok_overview::get_instance();

		add_action('init', [$this, 'create_cpt']);
	}

	public function create_cpt() {
		EM_lists::create_cpt(BOK.'liste', 'Bok', 'Bokliste', 'dashicons-book-alt');
	}
}