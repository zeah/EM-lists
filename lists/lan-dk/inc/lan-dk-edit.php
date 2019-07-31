<?php 
defined('ABSPATH') or die('Blank Space');


/*
*/
final class Lan_dk_edit {
	/* singleton */
	private static $instance = null;

	// private $name = 'emlanlistdk';	

	public static function get_instance() {
		if (self::$instance === null) self::$instance = new self();
		return self::$instance;
	}



	private function __construct() {


		add_action('manage_'.EMLANDK.'_posts_columns', array($this, 'column_head'));
		add_filter('manage_'.EMLANDK.'_posts_custom_column', array($this, 'custom_column'));
		add_filter('manage_edit-'.EMLANDK.'_sortable_columns', array($this, 'sort_column'));
		add_action('pre_get_posts', array($this, 'set_sort'));
		
		/* metabox, javascript */
		add_action('add_meta_boxes_'.EMLANDK, array($this, 'create_meta'));
		/* hook for page saving/updating */
		add_action('save_post', array($this, 'save'));


		// add_filter('emtheme_doc', array($this, 'add_doc'), 99);
		add_action('admin_enqueue_scripts', [$this, 'add_js']);

	}


	/**
	 *
	 */
	public function add_js() {
		$id = get_current_screen();
		if ($id->id != 'edit-'.EMLANDK) return;


		$args = [
			'post_type' 		=> EMLANDK,
			'posts_per_page' 	=> -1,
			'orderby'			=> [
										'meta_value_num' => 'ASC',
										'title' => 'ASC'
								   ]
		];

		$posts = get_posts($args);

		$po = [];

		foreach ($posts as $p) $po[$p->ID] = $p->post_name;

		$po['name'] = 'lan';

		$po['tax'] = EMLANDK.'type';

		wp_enqueue_script(EMLANDK.'_column', EM_LISTS_PLUGIN_URL.'assets/js/admin/emlist-column.js', ['jquery'], false, true);

		wp_localize_script(EMLANDK.'_column', 'listdata', json_encode($po));
	}

	/**
	 * wp filter for adding columns on ctp list page
	 * 
	 * @param  [array] $defaults [array going through wp filter]
	 * @return [array]           [array going through wp filter]
	 */
	public function column_head($defaults) {
		return EM_lists::custom_head($defaults, EMLANDK.'_sort');
	}


	/**
	 * filter for populating columns on ctp list page
	 * 
	 * @param  [array] $defaults [array going through wp filter]
	 * @return [array]           [array going through wp filter]
	 */
	public function custom_column($column_name) {
		global $post;
		EM_lists::custom_column($post->ID, EMLANDK, $column_name);
	}


	/**
	 * filter for sorting by columns on ctp list page
	 * 
	 * @param  [array] $defaults [array going through wp filter]
	 * @return [array]           [array going through wp filter]
	 */
	public function sort_column($columns) {
		return EM_lists::sort_column($columns, EMLANDK.'_sort');
	}

	public function set_sort($query) {
		Em_lists::set_sort($query, EMLANDK);
	}

	/*
		creates wordpress metabox
		adds javascript
	*/
	public function create_meta() {

		/* lan info meta */
		add_meta_box(
			EMLANDK.'_meta', // name
			'Lån Info', // title 
			[$this,'create_meta_box'], // callback
			EMLANDK // page
		);

		/* to show or not on front-end */
		add_meta_box(
			EMLANDK.'_exclude',
			'Aldri vis',
			[$this, 'exclude_meta_box'],
			EMLANDK,
			'side'
		);
		
		/* adding admin css and js */
		wp_enqueue_style(EMLANDK.'-admin-style', LAN_DK_PLUGIN_URL . 'assets/css/admin/em-lanlist-dk.css', array(), '1.0.0');
		wp_enqueue_script(EMLANDK.'-admin', LAN_DK_PLUGIN_URL . 'assets/js/admin/em-lanlist-dk.js', array(), '1.0.0', true);
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
		EM_list_edit::create_exclude_box($post, EMLANDK);
	}



	/**
	 * wp action when saving
	 */
	public function save($post_id) {
		EM_list_edit::save($post_id, EMLANDK);
	}

}