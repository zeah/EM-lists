<?php 
defined('ABSPATH') or die('Blank Space');


final class EM_list_edit {
	/* singleton */
	private static $instance = null;

	public static function get_instance() {
		if (self::$instance === null) self::$instance = new self();

		return self::$instance;
	}

	private function __construct() {
		// $this->wp_hooks();
	}

	private function wp_hooks() {

	}

	public static function create_meta_box($post, $name) {
		if (!$name) return;

		wp_nonce_field('em'.basename(__FILE__), $name.'_nonce');

		$meta = get_post_meta($post->ID, $name.'_data');
		$sort = get_post_meta($post->ID, $name.'_sort');
		$redirect = get_post_meta($post->ID, $name.'_redirect');
		if (!is_array($redirect)) $redirect = [];

		$tax = wp_get_post_terms($post->ID, $name.'type');

		$taxes = [];
		if (is_array($tax))
			foreach($tax as $t)
				array_push($taxes, $t->slug);

		$json = [
			// 'meta' => isset($meta[0]) ? $meta[0] : '',
			'meta' => isset($meta[0]) ? EM_Lists::sanitize($meta[0]) : '',
			$name.'_sort' => isset($sort[0]) ? floatval($sort[0]) : '',
			$name.'_redirect' => $redirect[0],
			'tax' => $taxes
		];

		$ameta = get_post_meta($post->ID);
		foreach($ameta as $key => $value)
			if (strpos($key, $name.'_sort_') !== false && isset($value[0])) $json[$key] = esc_html($value[0]);

		wp_localize_script($name.'-admin', $name.'_data', json_decode(json_encode($json), true));
		echo '<div class="'.$name.'-meta-container"></div>';
	}



	public static function create_exclude_box($post, $name) {
		$exclude = get_option($name.'_exclude');
		if (!is_array($exclude)) $exclude = [];


		$exclude_serp = get_option($name.'_exclude_serp');
		if (!is_array($exclude_serp)) $exclude_serp = [];


		echo '<p><input name="'.$name.'_exclude" id="'.$name.'_exc" type="checkbox"'.(array_search($post->ID, $exclude) !== false ? ' checked' : '').'><label for="'.$name.'_exc">Lån vil ikke vises på front-end når boksen er markert.</label></p>
		      <p><input name="'.$name.'_exclude_serp" id="'.$name.'_exc_serp" type="checkbox"'.(array_search($post->ID, $exclude_serp) !== false ? ' checked' : '').'><label for="'.$name.'_exc_serp">Ikke vis i internal SERP.</label></p>';
	}

	public static function save($post_id, $post_type) {
		if (!get_post_type($post_id) == $post_type) return;

		// is on admin screen
		if (!is_admin()) return;

		// user is logged in and has permission
		if (!current_user_can('edit_posts')) return;

		// nonce is sent
		if (!isset($_POST[$post_type.'_nonce'])) return;

		// nonce is checked
		if (!wp_verify_nonce($_POST[$post_type.'_nonce'], 'em'.basename(__FILE__))) return;

		// saves to wp option instead of post meta
		// when adding
		EM_lists::update_option($post_type.'_exclude', $post_id);
		EM_lists::update_option($post_type.'_exclude_serp', $post_id);
		// EM_lists::update_option($post_type.'_redirect', $post_id);

		// global $post;
		EM_list_edit::update_redirect($post_type);

		// data is sent, then sanitized and saved
		if (isset($_POST[$post_type.'_data'])) update_post_meta($post_id, $post_type.'_data', EM_lists::sanitize($_POST[$post_type.'_data']));
		if (isset($_POST[$post_type.'_sort'])) update_post_meta($post_id, $post_type.'_sort', floatval($_POST[$post_type.'_sort']));
		update_post_meta($post_id, $post_type.'_redirect', isset($_POST[$post_type.'_redirect']) ? true : false);

		// saving emlanlist_sort_***
		foreach($_POST as $key => $po) {
			if (strpos($key, $post_type.'_sort_') !== false)
				update_post_meta($post_id, sanitize_text_field(str_replace(' ', '', $key)), floatval($po));
		}
	}

	// public static function update_redirect($post_type, $p = null, $postfix = null) {
	public static function update_redirect($post_type) {
		global $post;
		$pn = $post->post_name;

		$url = $_POST[$post_type.'_data'];
		// wp_die('<xmp>'.print_r($url, true).'</xmp>');
		// if (!isset($url['bestill']) || !$url['bestill']) return;

		$opt = get_option($post_type.'_redirect');

		if (isset($_POST[$post_type.'_redirect'])) {
			if (isset($url['bestill']) && $url['bestill']) $opt[$pn] = $url['bestill'];
		}
		elseif (isset($opt[$pn])) unset($opt[$pn]);

		update_option($post_type.'_redirect', $opt);

		// if (!isset($_POST[$post_type.'_redirect']) || !$_POST[$post_type.'_redirect'])

		// if (!isset($url['_redirect']) || !$url['_redirect']) return;



		// if ()
		// $url = $_POST[$post_type.'_data'];

		// if (!is_array($url)) return;
		// if (!isset($url['bestill'])) return;

		// if ($postfix) $pf = $postfix;
		
		// else {
		// 	$pf = get_option('em_lists');
		// 	if (isset($pf['redir_pf']) && $pf['redir_pf']) $pf = $pf['redir_pf'];
		// 	else $pf = 'get';
		// }

		// $pf = '-'.ltrim($pf, '-');


		// if ($p) $post_name = $p;
		// else {
		// 	global $post;
		// 	$post_name = $post->post_name;
		// }


		// $red = $post_type.'_redirect';
		// $opt = get_option($red);

		// $url = $_POST[$post_type.'_data'];

		// if (isset($url['bestill']) && $url['bestill']) {
		// 	$data = ['url' => $url['bestill'], 'pf' => $pf];
		// 	$opt[$post_name] = $data;
		// }
		// else unset($opt[$post_name]);

		// // foreach ($opt as $key => $value)
		// 	// if (strpos($key, $post_name.'-')) unset()
		// // if (isset($_POST[$red]) && is_array($url) && isset($url['bestill'])) $opt[$post_name.'-fa'] = $url['bestill'];
		// // else unset($opt[$post_name.'-fa']);
		// update_option($red, $opt);
		// wp_die('<xmp>'.print_r($opt, true).'</xmp>');
	}
}