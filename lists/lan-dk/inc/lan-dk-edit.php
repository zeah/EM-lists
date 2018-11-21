<?php 
defined('ABSPATH') or die('Blank Space');


/*
*/
final class Lan_dk_edit {
	/* singleton */
	private static $instance = null;

	private $name = 'emlanlistdk';

	public static function get_instance() {
		if (self::$instance === null) self::$instance = new self();
		return self::$instance;
	}



	private function __construct() {


		add_action('manage_'.$this->name.'_posts_columns', array($this, 'column_head'));
		add_filter('manage_'.$this->name.'_posts_custom_column', array($this, 'custom_column'));
		add_filter('manage_edit-'.$this->name.'_sortable_columns', array($this, 'sort_column'));
		add_action('pre_get_posts', array($this, 'set_sort'));
		
		/* metabox, javascript */
		add_action('add_meta_boxes_'.$this->name, array($this, 'create_meta'));
		/* hook for page saving/updating */
		add_action('save_post', array($this, 'save'));


		add_filter('emtheme_doc', array($this, 'add_doc'), 99);

	}


	/**
	 * wp filter for adding columns on ctp list page
	 * 
	 * @param  [array] $defaults [array going through wp filter]
	 * @return [array]           [array going through wp filter]
	 */
	public function column_head($defaults) {
		return EM_lists::custom_head($defaults, $this->name.'_sort');
	}


	/**
	 * filter for populating columns on ctp list page
	 * 
	 * @param  [array] $defaults [array going through wp filter]
	 * @return [array]           [array going through wp filter]
	 */
	public function custom_column($column_name) {
		global $post;
		EM_lists::custom_column($post->ID, $this->name, $column_name);
	}


	/**
	 * filter for sorting by columns on ctp list page
	 * 
	 * @param  [array] $defaults [array going through wp filter]
	 * @return [array]           [array going through wp filter]
	 */
	public function sort_column($columns) {
		return EM_lists::sort_column($columns, $this->name.'_sort');
	}

	public function set_sort($query) {
		Em_lists::set_sort($query, $this->name);
	}

	/*
		creates wordpress metabox
		adds javascript
	*/
	public function create_meta() {

		/* lan info meta */
		add_meta_box(
			$this->name.'_meta', // name
			'Lån Info', // title 
			array($this,'create_meta_box'), // callback
			$this->name // page
		);

		/* to show or not on front-end */
		add_meta_box(
			$this->name.'_exclude',
			'Aldri vis',
			array($this, 'exclude_meta_box'),
			$this->name,
			'side'
		);
		
		/* adding admin css and js */
		wp_enqueue_style($this->name.'-admin-style', LAN_DK_PLUGIN_URL . 'assets/css/admin/em-lanlist-dk.css', array(), '1.0.0');
		wp_enqueue_script($this->name.'-admin', LAN_DK_PLUGIN_URL . 'assets/js/admin/em-lanlist-dk.js', array(), '1.0.0', true);
	}


	/*
		creates content in metabox
	*/
	public function create_meta_box($post) {
		EM_list_edit::create_meta_box($post, $post->post_type);
	}
 

 	/**
 	 * [exclude_meta_box description]
 	 */
	public function exclude_meta_box() {
		global $post;
		EM_list_edit::create_exclude_box($post, $this->name);
	}



	/**
	 * wp action when saving
	 */
	public function save($post_id) {
		EM_list_edit::save($post_id, $this->name);

		// post type is emlanlistse
		// if (!get_post_type($post_id) == 'emlanlistse') return;

		// // is on admin screen
		// if (!is_admin()) return;

		// // user is logged in and has permission
		// if (!current_user_can('edit_posts')) return;

		// // nonce is sent
		// if (!isset($_POST['emlanlistse_nonce'])) return;

		// // nonce is checked
		// if (!wp_verify_nonce($_POST['emlanlistse_nonce'], 'em'.basename(__FILE__))) return;

		// // saves to wp option instead of post meta
		// // when adding
		// $this->u_option('emlanlistse_exclude', $post_id);
		// $this->u_option('emlanlistse_exclude_serp', $post_id);
		// if (isset($_POST['emlanlistse_exclude'])) {
		// 	$option = get_option('emlanlistse_exclude');

		// 	// to avoid php error
		// 	if (!is_array($option)) $option = [];

		// 	// if not already added
		// 	if (array_search($post_id, $option) === false) {

		// 		// if to add to collection
		// 		if (is_array($option)) {
		// 			array_push($option, intval($post_id));

		// 			update_option('emlanlistse_exclude', $option);
		// 		}
				
		// 		// if to create collection (of one)
		// 		else update_option('emlanlistse_exclude', [$post_id]);
		// 	}
		// }
		// // when removing
		// else {
		// 	$option = get_option('emlanlistse_exclude');

		// 	if (array_search($post_id, $option) !== false) {
		// 		unset($option[array_search($post_id, $option)]);
		// 		update_option('emlanlistse_exclude', $option);
		// 	}
		// }

		// data is sent, then sanitized and saved
		// if (isset($_POST['emlanlistse_data'])) update_post_meta($post_id, 'emlanlistse_data', $this->sanitize($_POST['emlanlistse_data']));
		// if (isset($_POST['emlanlistse_sort'])) update_post_meta($post_id, 'emlanlistse_sort', floatval($_POST['emlanlistse_sort']));

		// // saving emlanlistse_sort_***
		// foreach($_POST as $key => $po) {
		// 	if (strpos($key, 'emlanlistse_sort_') !== false)
		// 		update_post_meta($post_id, sanitize_text_field(str_replace(' ', '', $key)), floatval($po));
		// }

	}

}