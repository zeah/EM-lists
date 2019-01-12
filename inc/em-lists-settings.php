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


		// add_action('update_option_em_lists', [$this, 'fixpf'], 10, 2);
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
		// add_settings_field('em-lists-kredittkort', 'Kredittkort', array($this, 'kredittkort'), 'em-lists-page', 'em-lists-section');
		// add_settings_field('em-lists-kredittkort-se', 'Kredittkort Sverige', array($this, 'kredittkort_se'), 'em-lists-page', 'em-lists-section');
		add_settings_field('em-lists-lan', 'Lån', [$this, 'list'], 'em-lists-page', 'em-lists-section', 'emlanlist');

		add_settings_field('em-lists-lan-se', 'Lån Sverige', [$this, 'list'], 'em-lists-page', 'em-lists-section', 'emlanlistse');

		add_settings_field('em-lists-lan-dk', 'Lån Danmark', [$this, 'list'], 'em-lists-page', 'em-lists-section', 'emlanlistdk');

		add_settings_field('em-lists-bok', 'Bokklubb', [$this, 'list'], 'em-lists-page', 'em-lists-section', 'bokliste');

		add_settings_field('em-lists-matkasse', 'Matkasse', [$this, 'list'], 'em-lists-page', 'em-lists-section', 'matkasselist');

		add_settings_section('em-lists-redir', 'Redirection', array($this, 'list_redir_section'), 'em-lists-page');
		add_settings_field('em-lists-rdpf', 'Url post-fix', array($this, 'redir_pf'), 'em-lists-page', 'em-lists-redir');

	}

	public function list_section() {
		// echo 'EM Lists';
	}

	public function list($t) {
		$data = get_option('em_lists');
		echo '<input type="checkbox" name="em_lists['.$t.']"'.(isset($data[$t]) ? ' checked' : '').'>';
	}

	public function list_redir_section() {
		echo 'Redirection';
	}

	public function redir_pf() {
		$data = get_option('em_lists');
		echo '<input type="text" name="em_lists[redir_pf]" value="'.($data['redir_pf'] ? esc_attr($data['redir_pf']) : '').'">';
	}

	/* adding css and js*/
	public function add_sands() {
	}

}