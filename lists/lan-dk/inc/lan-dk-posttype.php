<?php 
defined('ABSPATH') or die('Blank Space');

require_once 'lan-dk-taxonomy.php';
require_once 'lan-dk-edit.php';
require_once 'lan-dk-overview.php';

/**
 * 
 */
final class Lan_dk_posttype {
	/* singleton */
	private static $instance = null;

	public static function get_instance() {
		if (self::$instance === null) self::$instance = new self();
		return self::$instance;
	}

	private function __construct() {
		Lan_dk_edit::get_instance();
		Lan_dk_taxonomy::get_instance();
		Lan_dk_overview::get_instance();

		/* creates custom post type: emkort */
		add_action('init', [$this, 'create_cpt']);
	}
	/*
		create custom post type: emkort 
	*/
	public function create_cpt() {
		EM_lists::create_cpt(EMLANDK, 'Lån', 'Lån DK', 'dashicons-money');
	}
}