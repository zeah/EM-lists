<?php 
defined('ABSPATH') or die('Blank Space');


/*
*/
final class Lan_edit {
	/* singleton */
	private static $instance = null;

	// private $name = 'emlanlist';

	public static function get_instance() {
		if (self::$instance === null) self::$instance = new self();
		return self::$instance;
	}



	private function __construct() {
		add_action('manage_'.EMLAN.'_posts_columns', [$this, 'column_head']);
		add_filter('manage_'.EMLAN.'_posts_custom_column', [$this, 'custom_column']);
		add_filter('manage_edit-'.EMLAN.'_sortable_columns', [$this, 'sort_column']);
		add_action('pre_get_posts', [$this, 'set_sort']);

		/* metabox, javascript */
		add_action('add_meta_boxes_'.EMLAN, [$this, 'create_meta']);
		/* hook for page saving/updating */
		add_action('save_post', [$this, 'save']);
	}


	/**
	 * wp filter for adding columns on ctp list page
	 * 
	 * @param  [array] $defaults [array going through wp filter]
	 * @return [array]           [array going through wp filter]
	 */
	public function column_head($defaults) {
		return EM_lists::custom_head($defaults, EMLAN.'_sort');
	}


	/**
	 * filter for populating columns on ctp list page
	 * 
	 * @param  [array] $defaults [array going through wp filter]
	 * @return [array]           [array going through wp filter]
	 */
	public function custom_column($column_name) {
		global $post;
		EM_lists::custom_column($post->ID, EMLAN, $column_name);
	}


	/**
	 * filter for sorting by columns on ctp list page
	 * 
	 * @param  [array] $defaults [array going through wp filter]
	 * @return [array]           [array going through wp filter]
	 */
	public function sort_column($columns) {
		return EM_lists::sort_column($columns, EMLAN.'_sort');
	}

	/* telling wp how to sort the meta values */
	public function set_sort($query) {
		Em_lists::set_sort($query, EMLAN);
	}



	/*
		creates wordpress metabox
		adds javascript
	*/
	public function create_meta() {

		/* lan info meta */
		add_meta_box(
			EMLAN.'_meta', // name
			'Lån Info', // title 
			array($this,'create_meta_box'), // callback
			EMLAN // page
		);

		/* to show or not on front-end */
		add_meta_box(
			EMLAN.'_exclude',
			'Aldri vis',
			array($this, 'exclude_meta_box'),
			EMLAN,
			'side'
		);
		
		/* adding admin css and js */
		wp_enqueue_style(EMLAN.'-admin-style', LANLIST_PLUGIN_URL . 'assets/css/admin/em-lanlist.css', array(), '1.0.3');
		wp_enqueue_script(EMLAN.'-admin', LANLIST_PLUGIN_URL . 'assets/js/admin/em-lanlist.js', array(), '1.0.4', true);
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
		EM_list_edit::create_exclude_box($post, EMLAN);
	}


	/**
	 * wp action when saving
	 */
	public function save($post_id) {
		EM_list_edit::save($post_id, EMLAN);
	}
}