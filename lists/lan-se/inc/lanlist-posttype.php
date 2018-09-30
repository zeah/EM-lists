<?php 
defined('ABSPATH') or die('Blank Space');

require_once 'lanlist-taxonomy.php';
require_once 'lanlist-edit.php';
require_once 'lanlist-overview.php';

/**
 * 
 */
final class Lanlist_posttype {
	/* singleton */
	private static $instance = null;

	public static function get_instance() {
		if (self::$instance === null) self::$instance = new self();
		return self::$instance;
	}

	private function __construct() {
		Lanlist_edit::get_instance();
		Lanlist_taxonomy::get_instance();
		Lanlist_overview::get_instance();

		/* creates custom post type: emkort */
		add_action('init', array($this, 'create_cpt'));
	}
	/*
		create custom post type: emkort 
	*/
	public function create_cpt() {

		EM_lists::create_cpt('emlanlistse', 'Lånlist');
	}
}