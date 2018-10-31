<?php 
defined('ABSPATH') or die('Blank Space');

require_once 'lan-se-taxonomy.php';
require_once 'lan-se-edit.php';
require_once 'lan-se-overview.php';

/**
 * 
 */
final class Lan_se_posttype {
	/* singleton */
	private static $instance = null;

	public static function get_instance() {
		if (self::$instance === null) self::$instance = new self();
		return self::$instance;
	}

	private function __construct() {
		Lan_se_edit::get_instance();
		Lan_se_taxonomy::get_instance();
		Lan_se_overview::get_instance();

		/* creates custom post type: emkort */
		add_action('init', array($this, 'create_cpt'));
	}
	/*
		create custom post type: emkort 
	*/
	public function create_cpt() {

		EM_lists::create_cpt('emlanlistse', 'Lån', 'Lånlist', 'dashicons-money');
	}
}