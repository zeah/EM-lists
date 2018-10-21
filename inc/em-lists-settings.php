<?php 
defined('ABSPATH') or die('Blank Space');


final class EM_list_settings {
	/* singleton */
	private static $instance = null;

	public static function get_instance() {
		if (self::$instance === null) self::$instance = new self();

		return self::$instance;
	}

	private function __construct() {
		$this->wp_hooks();
	}

	/**
	 * wp hooks
	 */
	private function wp_hooks() {
		add_action('admin_menu', array($this, 'add_menu'));
		add_action('admin_init', array($this, 'register_settings'));

		add_action('admin_enqueue_scripts', array($this, 'add_sands'));

	}


	/**
	 * adding submenu page
	 */
	public function add_menu() {
		add_submenu_page('options-general.php', 'EM Lists', 'Lists', 'manage_options', 'em-lists-page', array($this, 'page_callback'));
	}

	/* echoing page */
	public function page_callback() {
		echo '<form action="options.php" method="POST">';
		settings_fields('em-lists-settings');
		do_settings_sections('em-lists-page');
		submit_button('save');
		echo '</form>';
	}

	/* register settings */
	public function register_settings() {
		register_setting('em-lists-settings', 'em_lists', ['sanitize_callback' => array('EM_lists', 'sanitize')]);

		add_settings_section('em-lists-section', 'EM Lists', array($this, 'list_section'), 'em-lists-page');
		add_settings_field('em-lists-kredittkort', 'Kredittkort', array($this, 'kredittkort'), 'em-lists-page', 'em-lists-section');
		add_settings_field('em-lists-kredittkort-se', 'Kredittkort Sverige', array($this, 'kredittkort_se'), 'em-lists-page', 'em-lists-section');
		add_settings_field('em-lists-lan', 'Lån', array($this, 'lan'), 'em-lists-page', 'em-lists-section');
		add_settings_field('em-lists-lan-se', 'Lån Sverige', array($this, 'lan_se'), 'em-lists-page', 'em-lists-section');
	}

	public function list_section() {
		// echo 'EM Lists';
	}

	public function kredittkort() {
		$data = get_option('em_lists');
		echo '<input type="checkbox" name="em_lists[kredittkort]"'.(isset($data['kredittkort']) ? ' checked' : '').'>';
	}

	public function kredittkort_se() {
		$data = get_option('em_lists');
		echo '<input type="checkbox" name="em_lists[kredittkort_se]"'.(isset($data['kredittkort_se']) ? ' checked' : '').'>';
	}

	public function lan() {
		$data = get_option('em_lists');
		echo '<input type="checkbox" name="em_lists[lan]"'.(isset($data['lan']) ? ' checked' : '').'>';
	}

	public function lan_se() {
		$data = get_option('em_lists');
		echo '<input type="checkbox" name="em_lists[lan_se]"'.(isset($data['lan_se']) ? ' checked' : '').'>';
	}

	/* adding css and js*/
	public function add_sands() {
	}

}