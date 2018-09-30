<?php 
defined('ABSPATH') or die('Blank Space');

require_once 'lan-taxonomy.php';
require_once 'lan-edit.php';
require_once 'lan-overview.php';

/**
 * 
 */
final class Lan_posttype {
	/* singleton */
	private static $instance = null;

	public static function get_instance() {
		if (self::$instance === null) self::$instance = new self();
		return self::$instance;
	}

	private function __construct() {
		Lan_edit::get_instance();
		Lan_taxonomy::get_instance();
		Lan_overview::get_instance();

		/* creates custom post type: emkort */
		add_action('init', array($this, 'create_cpt'));
	}
	/*
		create custom post type: emkort 
	*/
	public function create_cpt() {
		EM_lists::create_cpt('emlanlist', 'Lån');
	}
}