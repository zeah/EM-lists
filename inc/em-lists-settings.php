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

		// add_action('admin_enqueue_scripts', array($this, 'add_sands'));
		add_action('admin_enqueue_scripts', [$this, 'add_sands']);



		// add_action('update_option_em_lists', [$this, 'fixpf'], 10, 2);
	}

	public function add_sands($hook) {
		if ($hook != 'plugins_page_em-lists-page') return;

        wp_enqueue_style('emaxowl-se-admin', EM_LISTS_PLUGIN_URL.'assets/css/admin/emlist-admin.css', [], '1.0.0');
        wp_enqueue_script('emaxowl-se-admin', EM_LISTS_PLUGIN_URL.'assets/js/admin/emlist-settings.js', ['jquery'], '1.0.1', true);
	}

	/**
	 * adding submenu page
	 */
	public function add_menu() {
		add_submenu_page(	
							'plugins.php', 'EM Lists', 'Lists', 'manage_options', 
							'em-lists-page', [$this, 'page_callback']
						);
		// add_submenu_page('options-general.php', 'EM Lists', 'Lists', 'manage_options', 'em-lists-page', [$this, 'page_callback']);
	}

	/* echoing page */
	public function page_callback() {
		echo '<form action="options.php" class="emlistform" method="POST">';
		settings_fields('em-lists-settings');
		do_settings_sections('em-lists-page');
		submit_button('save');
		echo '</form>';
	}

	/* register settings */
	public function register_settings() {
		register_setting('em-lists-settings', 'em_lists', ['sanitize_callback' => ['EM_lists', 'sanitize']]);

		add_settings_section('em-lists-section-ga', 'EM Lists', [$this, 'list_section_ga'], 'em-lists-page');
		add_settings_field('em-lists-gaid', 'Google Analytics ID', array($this, 'list_text'), 'em-lists-page', 'em-lists-section-ga', 'gaid');
		
		add_settings_section('em-lists-section', 'EM Lists', [$this, 'list_section'], 'em-lists-page');

		add_settings_field('em-lists-kredittkort', 'Kredittkort', array($this, 'list'), 'em-lists-page', 'em-lists-section', 'emkredittkort');
		add_settings_field('em-lists-kredittkort-text', 'Knapptekst', [$this, 'list_text'], 'em-lists-page', 'em-lists-section', 'emkredittkort_text');
		add_settings_field('em-lists-kredittkort-ab', 'Aktiver A/B testing', [$this, 'list_text'], 'em-lists-page', 'em-lists-section', 'emkredittkort_ab');

		add_settings_field('em-lists-kredittkort-se', 'Kredittkort SE', array($this, 'list'), 'em-lists-page', 'em-lists-section', 'emkredittkortse');
		add_settings_field('em-lists-kredittkort-se-text', 'Knapptekst', [$this, 'list_text'], 'em-lists-page', 'em-lists-section', 'emkredittkortse_text');
		add_settings_field('em-lists-kredittkort-se-ab', 'Aktiver A/B testing', [$this, 'list_text'], 'em-lists-page', 'em-lists-section', 'emkredittkortse_ab');
		
		// add_settings_field('em-lists-kredittkort-se', 'Kredittkort Sverige', array($this, 'kredittkort_se'), 'em-lists-page', 'em-lists-section');
		
		add_settings_field('em-lists-lan', 'Lån', [$this, 'list'], 'em-lists-page', 'em-lists-section', 'emlanlist');
		add_settings_field('em-lists-lan-text', 'Knapptekst', [$this, 'list_text'], 'em-lists-page', 'em-lists-section', 'emlanlist_text');
		add_settings_field('em-lists-lan-ab', 'Aktiver A/B testing', [$this, 'list_text'], 'em-lists-page', 'em-lists-section', 'emlanlist_ab');

		add_settings_field('em-lists-lan-se', 'Lån Sverige', [$this, 'list'], 'em-lists-page', 'em-lists-section', 'emlanlistse');
		add_settings_field('em-lists-lan-se-text', 'Knapptekst', [$this, 'list_text'], 'em-lists-page', 'em-lists-section', 'emlanlistse_text');
		add_settings_field('em-lists-lan-se-ab', 'Aktiver A/B testing', [$this, 'list_text'], 'em-lists-page', 'em-lists-section', 'emlanlistse_ab');

		add_settings_field('em-lists-lan-dk', 'Lån Danmark', [$this, 'list'], 'em-lists-page', 'em-lists-section', 'emlanlistdk');
		add_settings_field('em-lists-lan-dk-text', 'Knapptekst', [$this, 'list_text'], 'em-lists-page', 'em-lists-section', 'emlanlistdk_text');
		add_settings_field('em-lists-lan-dk-ab', 'Aktiver A/B testing', [$this, 'list_text'], 'em-lists-page', 'em-lists-section', 'emlanlistdk_ab');

		add_settings_field('em-lists-bok', 'Bokklubb', [$this, 'list'], 'em-lists-page', 'em-lists-section', 'bokliste');
		add_settings_field('em-lists-bok-text', 'Knapptekst', [$this, 'list_text'], 'em-lists-page', 'em-lists-section', 'boklist_text');
		add_settings_field('em-lists-bok-ab', 'Aktiver A/B testing', [$this, 'list_text'], 'em-lists-page', 'em-lists-section', 'boklist_ab');

		add_settings_field('em-lists-matkasse', 'Matkasse', [$this, 'list'], 'em-lists-page', 'em-lists-section', 'matkasselist');
		add_settings_field('em-lists-matkasse-text', 'Knapptekst', [$this, 'list_text'], 'em-lists-page', 'em-lists-section', 'matkasselist_text');
		add_settings_field('em-lists-matkasse-ab', 'Aktiver A/B testing', [$this, 'list_text'], 'em-lists-page', 'em-lists-section', 'matkasselist_ab');

		// add_settings_section('em-lists-redir', 'Redirection', array($this, 'list_redir_section'), 'em-lists-page');
		// add_settings_field('em-lists-rdpf', 'Url post-fix', array($this, 'redir_pf'), 'em-lists-page', 'em-lists-redir');

	}

	public function list_section_ga() {
		echo '<h2>Google Analytics</h2>';
	}

	public function list_section() {
		echo '<h2>Activate list plugins:</h2>';
	}

	public function list($t) {
		$data = get_option('em_lists');
		printf(
			'<input type="checkbox" name="em_lists[%1$s]"%2$s>',
			$t,
			isset($data[$t]) ? ' checked' : ''
		);

		// echo '<div class=""><input type="checkbox" name="em_lists['.$t.']"'.(isset($data[$t]) ? ' checked' : '').'></div>';
	}


	public function list_text($t) {
		$data = get_option('em_lists');
		printf(
			'<input type="text" name="em_lists[%1$s]"" value="%2$s">',
			$t,
			isset($data[$t]) ? $data[$t] : ''
		);
	}

	// public function list_redir_section() {
	// 	echo 'Redirection';
	// }

	// public function redir_pf() {
	// 	$data = get_option('em_lists');
	// 	echo '<input type="text" name="em_lists[redir_pf]" value="'.($data['redir_pf'] ? esc_attr($data['redir_pf']) : '').'">';
	// }


}