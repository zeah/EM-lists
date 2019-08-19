<?php 


final class Bok_links {
	private static $instance = null;

	public static function get_instance() {
		if (self::$instance === null) self::$instance = new self();

		return self::$instance;
	} 

	public function __construct() {
		add_action('admin_menu', [$this, 'add_menu']);
	}


	public function add_page($name) {
		wp_enqueue_style('em-'.BOK.'-admin-style', BOK_PLUGIN_URL . 'assets/css/admin/em-bok.css', [], '1.0.2');

		$posts = get_posts(['post_type' => BOK.'liste', 'posts_per_page' => -1]);

		echo '<table class="'.BOK.'-overview-links"><tr><th></th><th>Name</th><th>Button Link</th></tr>';

		$site = get_site_url();

		foreach ($posts as $p) {
			$meta = get_post_meta($p->ID, BOK.'liste_data');

			$m = isset($meta[0]['bestill']) ? $meta[0]['bestill'] : 'no link';
			printf('
				<tr><td><a target="_blank" rel=noopener href="%s/wp-admin/post.php?post=%s&action=edit">link</a></td><td>%s</td><td>%s</td></tr>', 
				$site,
				$p->ID, 
				$p->post_name, 
				$m
			);
		}

		echo '</table>';
	}

	public function add_menu($name) {
		add_submenu_page('edit.php?post_type='.BOK.'liste', 'Links', 'Links', 'manage_options', BOK.'-links', [$this, 'add_page']);
	}
}