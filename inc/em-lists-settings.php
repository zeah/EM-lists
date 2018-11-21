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
		add_settings_field('em-lists-lan', 'Lån', array($this, 'lan'), 'em-lists-page', 'em-lists-section');
		add_settings_field('em-lists-lan-se', 'Lån Sverige', array($this, 'lan_se'), 'em-lists-page', 'em-lists-section');
		add_settings_field('em-lists-lan-dk', 'Lån Danmark', array($this, 'lan_dk'), 'em-lists-page', 'em-lists-section');
		add_settings_field('em-lists-bok', 'Bokklubb', array($this, 'bok'), 'em-lists-page', 'em-lists-section');

		add_settings_section('em-lists-redir', 'Redirection', array($this, 'list_redir_section'), 'em-lists-page');
		add_settings_field('em-lists-rdpf', 'Url post-fix', array($this, 'redir_pf'), 'em-lists-page', 'em-lists-redir');

	}

	public function list_section() {
		// echo 'EM Lists';
	}

	public function kredittkort() {
		$data = get_option('em_lists');
		echo '<input type="checkbox" name="em_lists[emkredittkort]"'.(isset($data['emkredittkort']) ? ' checked' : '').'>';
	}

	public function kredittkort_se() {
		$data = get_option('em_lists');
		echo '<input type="checkbox" name="em_lists[emkredittkort_se]"'.(isset($data['emkredittkort_se']) ? ' checked' : '').'>';
	}

	public function lan() {
		$data = get_option('em_lists');
		echo '<input type="checkbox" name="em_lists[emlanlist]"'.(isset($data['emlanlist']) ? ' checked' : '').'>';
	}

	public function lan_se() {
		$data = get_option('em_lists');
		echo '<input type="checkbox" name="em_lists[emlanlistse]"'.(isset($data['emlanlistse']) ? ' checked' : '').'>';
	}

	public function lan_dk() {
		$data = get_option('em_lists');
		echo '<input type="checkbox" name="em_lists[emlanlistdk]"'.(isset($data['emlanlistdk']) ? ' checked' : '').'>';
	}

	public function bok() {
		$data = get_option('em_lists');
		echo '<input type="checkbox" name="em_lists[bokliste]"'.(isset($data['bokliste']) ? ' checked' : '').'>';
	}

	public function list_redir_section() {
		echo 'Redirection';
	}

	public function redir_pf() {
		$data = get_option('em_lists');
		echo '<input type="text" name="em_lists[redir_pf]" value="'.($data['redir_pf'] ? esc_attr($data['redir_pf']) : '').'">';
	}

	// public function fixpf($old, $new) {

		// if (!is_array($new)) return;

		// if (!isset($new['redir_pf']) || !$new['redir_pf']) return;

		// foreach($new as $key => $value) {
		// 	if ($value == 'on') {
		// 		$args = [
		// 			'post_type' => $key,
		// 			'posts_per_page' => -1
		// 		];

		// 		$posts = get_posts($args);

		// 		foreach ($posts as $p)
		// 			EM_list_edit::update_option($key, $p, $new['redir_pf']);
		// 	}
		// }
	// }

	/* adding css and js*/
	public function add_sands() {
	}

}