<?php
defined( 'ABSPATH' ) or die( 'Blank Space' ); 
/*
	REGISTERING TAXONOMY
*/
final class Kredittkort_se_taxonomy {
	/* singleton */
	private static $instance = null;

	public static function get_instance() {
		if (self::$instance === null) self::$instance = new self();

		return self::$instance;
	}

	private function __construct() {
		/* creates taxonomy: korttype */
		add_action('init', [$this, 'create_tax']);
	}
	
	/*
		creates taxonomy: korttype 
		for custom post type: emkort
	*/
	public function create_tax() {
		EM_list_tax::create_tax(KREDITTKORT_SE.'type', KREDITTKORT_SE, 'Kredittkort Type', 'Kredittkort Type');
	}
}