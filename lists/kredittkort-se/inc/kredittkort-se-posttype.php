<?php 
defined('ABSPATH') or die('Blank Space');

require_once 'kredittkort-se-taxonomy.php';
require_once 'kredittkort-se-edit.php';
require_once 'kredittkort-se-overview.php';

/**
 * 
 */
final class Kredittkort_se_posttype {
	/* singleton */
	private static $instance = null;

	public static function get_instance() {
		if (self::$instance === null) self::$instance = new self();
		return self::$instance;
	}

	private function __construct() {
		Kredittkort_se_edit::get_instance();
		Kredittkort_se_taxonomy::get_instance();
		Kredittkort_se_overview::get_instance();

		/* creates custom post type: emkort */
		add_action('init', [$this, 'create_cpt']);
	}
	/*
		create custom post type: emkort 
	*/
	public function create_cpt() {

		EM_lists::create_cpt(KREDITTKORT_SE, 'Kredittkort', 'Kredittkort SE', 'dashicons-money');
	}
}