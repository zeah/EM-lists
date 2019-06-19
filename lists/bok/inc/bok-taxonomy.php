<?php
defined( 'ABSPATH' ) or die( 'Blank Space' ); 
/*
	REGISTERING TAXONOMY
*/
final class Bok_taxonomy {
	/* singleton */
	private static $instance = null;

	public static function get_instance() {
		if (self::$instance === null) self::$instance = new self();

		return self::$instance;
	}

	private function __construct() {
		add_action('init', [$this, 'create_tax']);
	}
	
	public function create_tax() {
		EM_list_tax::create_tax(BOK.'listetype', BOK.'liste', 'Boklist Type', 'Boklist Type');
	}
}