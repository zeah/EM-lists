<?php 

/*
Plugin Name: EM Lists
Description: Lists
Version: 0.0.6
GitHub Plugin URI: zeah/EM-lists
*/

defined('ABSPATH') or die('Blank Space');

require_once 'inc/em-lists-redirect.php';
require_once 'inc/em-lists-settings.php';
require_once 'inc/em-lists-shortcode.php';
require_once 'inc/em-lists-tax.php';
require_once 'inc/em-lists-cookie.php';
require_once 'inc/em-lists-tracking.php';
require_once 'inc/em-lists-edit.php';

require_once 'lists/lan/em-lanlist.php';
require_once 'lists/bok/em-bok.php';
require_once 'lists/lan-se/em-lanlist-se.php';
require_once 'lists/kredittkort/em-kredittkort.php';
// require_once 'lists/kredittkort-se/em-kredittkortlist.php';
// require_once 'lists/casino/em-casino.php';

// constant for plugin location
define('EM_LISTS_PLUGIN_URL', plugin_dir_url(__FILE__));




function init_emlistscode() {

	EM_lists::get_instance();

}
add_action('plugins_loaded', 'init_emlistscode');

final class EM_lists {
	/* singleton */
	private static $instance = null;

	public static function get_instance() {
		if (self::$instance === null) self::$instance = new self();

		return self::$instance;
	}

	private function __construct() {
		EM_list_redirect::get_instance();
		EM_list_cookie::get_instance();
		EM_list_settings::get_instance();
		// EM_list_edit::get_instance();
		
		$this->add_plugins();
	}


	private function add_plugins() {
		$data = get_option('em_lists');

		if (!is_array($data)) return;

		if (isset($data['emkredittkort'])) EM_kredittkort::get_instance();
		// if (isset($data['kredittkort_se'])) EM_kredittkortlist::get_instance();
		if (isset($data['emlanlist'])) EM_lan::get_instance();
		if (isset($data['emlanlistse'])) EM_se_lan::get_instance();
		if (isset($data['bokliste'])) EM_bok::get_instance();
		// if (isset($data['casino'])) EM_casino::get_instance();

	}

	public static function sanitize($data) {
		if (!is_array($data)) return sanitize_text_field($data);

		$d = [];
		foreach($data as $key => $value)
			$d[$key] = EM_Lists::sanitize($value);

		return $d;
	}

	/**
	 * updating array option
	 * @param  Array $data  The Array to be updated
	 * @param  String $value Which to be added or removed
	 */
	public static function update_option($data, $value) {
		$option = get_option($data);
		if (!is_array($option)) $option = []; // to avoid php error
		
		$value = intval($value);

		if (isset($_POST[$data])) {

			// if not already added
			if (array_search($value, $option) === false) {

				// if to add to collection
				if (is_array($option)) {
					array_push($option, $value);
					update_option($data, $option);
				}
				
				// if to create collection (of one)
				else update_option($data, [$value]);
			}
		}
		// when removing
		else {
			// $option = get_option($data);

			if (array_search($value, $option) !== false) {
				unset($option[array_search($value, $option)]);
				update_option($data, $option);
			}
		}
	}

	public static function create_cpt($name = null, $sing = null, $plur = null, $icon = null) {

		if (!$name || !$sing) return false;

		if (!$plur) $plur = $sing;

		// $plur = 'Lånlist';
		// $sing = 'Lånlist';
	
		$labels = array(
			'name'               => __( $plur, 'text-domain' ),
			'singular_name'      => __( $sing, 'text-domain' ),
			'add_new'            => _x( 'Add New '.$sing, 'text-domain', 'text-domain' ),
			'add_new_item'       => __( 'Add New '.$sing, 'text-domain' ),
			'edit_item'          => __( 'Edit '.$sing, 'text-domain' ),
			'new_item'           => __( 'New '.$sing, 'text-domain' ),
			'view_item'          => __( 'View '.$sing, 'text-domain' ),
			'search_items'       => __( 'Search '.$plur, 'text-domain' ),
			'not_found'          => __( 'No '.$plur.' found', 'text-domain' ),
			'not_found_in_trash' => __( 'No '.$plur.' found in Trash', 'text-domain' ),
			'parent_item_colon'  => __( 'Parent '.$sing.':', 'text-domain' ),
			'menu_name'          => __( $plur, 'text-domain' ),
		);
	
		$args = array(
			'labels'              => $labels,
			'hierarchical'        => false,
			'description'         => 'description',
			'taxonomies'          => array(),
			'public'              => false,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'show_in_admin_bar'   => true,
			'menu_position'       => 30,
			'menu_icon' 		  => $icon,
			'show_in_nav_menus'   => false,
			'publicly_queryable'  => false,
			'exclude_from_search' => false,
			'has_archive'         => false,
			'query_var'           => true,
			'can_export'          => true,
			'rewrite'             => false,
			'capability_type'     => 'post',
			'supports'            => array(
				'title',
				'thumbnail',
			),
		);
		
		register_post_type($name, $args);
	}

	/* add custom column */
	public static function custom_head($default, $name) {
		$default[$name] = __('Sorting Order');
		return $default;		
	}

	/* populating sort column */
	public static function custom_column($post_id, $post_type, $column_name) {
		if ($column_name == $post_type.'_sort') {
			$q_out = null;
			parse_str($_SERVER['QUERY_STRING'], $q_out);

			$meta = $post_type.'_sort';
			if (isset($q_out[$post_type.'type'])) $meta = $meta.'_'.$q_out[$post_type.'type'];

			$meta = get_post_meta($post_id, $meta);
			if (isset($meta[0])) echo $meta[0];
		}	
	}

	/* filter for sorting column */
	public static function sort_column($columns, $name) {
		$columns[$name] = $name;
		return $columns;
	}

	/* telling wp how to sort meta data */
	public static function set_sort($query, $post_type) {
		// if (!isset($query->query['post_type'])) return;

		// $post_type = $query->query['post_type'];

	    if (!is_admin()) return;
		if (!$query->is_main_query()) return;
		if ($query->get('post_type') != $post_type) return;

		$sort = $post_type.'_sort';
		if ($query->get($post_type.'type')) $sort .= '_'.$query->get($post_type.'type');

		$query->set('order_by', 'meta_value');
		$query->set('meta_key', $sort);
	    $query->set('meta_type', 'numeric');
	 
	}


	/* creating the meta box and adding json meta data */
	// public static function create_meta_box($post, $name) {

	// 	if (!$name) return;

	// 	wp_nonce_field('em'.basename(__FILE__), $name.'_nonce');

	// 	$meta = get_post_meta($post->ID, $name.'_data');
	// 	$sort = get_post_meta($post->ID, $name.'_sort');

	// 	$tax = wp_get_post_terms($post->ID, $name.'type');

	// 	$taxes = [];
	// 	if (is_array($tax))
	// 		foreach($tax as $t)
	// 			array_push($taxes, $t->slug);

	// 	$json = [
	// 		'meta' => isset($meta[0]) ? $meta[0] : '',
	// 		'meta' => isset($meta[0]) ? EM_Lists::sanitize($meta[0]) : '',
	// 		$name.'_sort' => isset($sort[0]) ? floatval($sort[0]) : '',
	// 		'tax' => $taxes
	// 	];

	// 	$ameta = get_post_meta($post->ID);
	// 	foreach($ameta as $key => $value)
	// 		if (strpos($key, $name.'_sort_') !== false && isset($value[0])) $json[$key] = esc_html($value[0]);


	// 	wp_localize_script($name.'-admin', $name.'_data', json_decode(json_encode($json), true));
	// 	echo '<div class="'.$name.'-meta-container"></div>';
	// }

	/* adding exclude meta box */
	// public static function create_exclude_box($post, $name) {
	// 	$exclude = get_option($name.'_exclude');
	// 	if (!is_array($exclude)) $exclude = [];


	// 	$exclude_serp = get_option($name.'_exclude_serp');
	// 	if (!is_array($exclude_serp)) $exclude_serp = [];


	// 	echo '<p><input name="'.$name.'_exclude" id="'.$name.'_exc" type="checkbox"'.(array_search($post->ID, $exclude) !== false ? ' checked' : '').'><label for="'.$name.'_exc">Lån vil ikke vises på front-end når boksen er markert.</label></p>
	// 	      <p><input name="'.$name.'_exclude_serp" id="'.$name.'_exc_serp" type="checkbox"'.(array_search($post->ID, $exclude_serp) !== false ? ' checked' : '').'><label for="'.$name.'_exc_serp">Ikke vis i internal SERP.</label></p>';
	// }


	/* saving post meta */
	// public static function save($post_id, $post_type) {
	// 	if (!get_post_type($post_id) == $post_type) return;

	// 	// is on admin screen
	// 	if (!is_admin()) return;

	// 	// user is logged in and has permission
	// 	if (!current_user_can('edit_posts')) return;

	// 	// nonce is sent
	// 	if (!isset($_POST[$post_type.'_nonce'])) return;

	// 	// nonce is checked
	// 	if (!wp_verify_nonce($_POST[$post_type.'_nonce'], 'em'.basename(__FILE__))) return;

	// 	// saves to wp option instead of post meta
	// 	// when adding
	// 	EM_lists::update_option($post_type.'_exclude', $post_id);
	// 	EM_lists::update_option($post_type.'_exclude_serp', $post_id);

	// 	// data is sent, then sanitized and saved
	// 	if (isset($_POST[$post_type.'_data'])) update_post_meta($post_id, $post_type.'_data', EM_lists::sanitize($_POST[$post_type.'_data']));
	// 	if (isset($_POST[$post_type.'_sort'])) update_post_meta($post_id, $post_type.'_sort', floatval($_POST[$post_type.'_sort']));

	// 	// saving emlanlist_sort_***
	// 	foreach($_POST as $key => $po) {
	// 		if (strpos($key, $post_type.'_sort_') !== false)
	// 			update_post_meta($post_id, sanitize_text_field(str_replace(' ', '', $key)), floatval($po));
	// 	}
	// }

}

