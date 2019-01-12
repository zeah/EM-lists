<?php 
defined('ABSPATH') or die('Blank Space');

require_once 'matkasse-taxonomy.php';
require_once 'matkasse-edit.php';
require_once 'matkasse-overview.php';

/**
 * 
 */
final class Matkasse_posttype {
	/* singleton */
	private static $instance = null;

	public static function get_instance() {
		if (self::$instance === null) self::$instance = new self();
		return self::$instance;
	}

	private function __construct() {
		Matkasse_edit::get_instance();
		Matkasse_taxonomy::get_instance();
		Matkasse_overview::get_instance();

		/* creates custom post type: emkort */
		add_action('init', array($this, 'create_cpt'));
	}
	/*
		create custom post type: emkort 
	*/
	public function create_cpt() {
		EM_lists::create_cpt('matkasselist', 'Matkasse', 'Mat NO', 'dashicons-carrot');
	}
}