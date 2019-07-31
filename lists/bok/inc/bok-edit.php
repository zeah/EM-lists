<?php 
defined('ABSPATH') or die('Blank Space');


/*
*/
final class Bok_edit {
	/* singleton */
	private static $instance = null;

	// private $name = 'bokliste';

	public static function get_instance() {
		if (self::$instance === null) self::$instance = new self();
		return self::$instance;
	}



	private function __construct() {


		add_action('manage_'.BOK.'_posts_columns', [$this, 'column_head']);
		add_filter('manage_'.BOK.'_posts_custom_column', [$this, 'custom_column']);
		add_filter('manage_edit-'.BOK.'_sortable_columns', [$this, 'sort_column']);
		add_action('pre_get_posts', [$this, 'set_sort']);
		
		/* metabox, javascript */
		add_action('add_meta_boxes_'.BOK, [$this, 'create_meta']);
		/* hook for page saving/updating */
		add_action('save_post', [$this, 'save']);


		// add_filter('emtheme_doc', [$this, 'add_doc'], 99);
		add_action('admin_enqueue_scripts', [$this, 'add_js']);

	}


	public function add_js() {
		$id = get_current_screen();
		if ($id->id != 'edit-'.BOK) return;


		$args = [
			'post_type' 		=> BOK,
			'posts_per_page' 	=> -1,
			'orderby'			=> [
										'meta_value_num' => 'ASC',
										'title' => 'ASC'
								   ]
		];

		$posts = get_posts($args);

		$po = [];

		foreach ($posts as $p) $po[$p->ID] = $p->post_name;

		$po['name'] = 'bok';

		$po['tax'] = BOK.'type';

		wp_enqueue_script(BOK.'_column', EM_LISTS_PLUGIN_URL.'assets/js/admin/emlist-column.js', ['jquery'], false, true);

		wp_localize_script(BOK.'_column', 'listdata', json_encode($po));
	}

	/**
	 * wp filter for adding columns on ctp list page
	 * 
	 * @param  [array] $defaults [array going through wp filter]
	 * @return [array]           [array going through wp filter]
	 */
	public function column_head($defaults) {
		return EM_lists::custom_head($defaults, BOK.'_sort');
	}


	/**
	 * filter for populating columns on ctp list page
	 * 
	 * @param  [array] $defaults [array going through wp filter]
	 * @return [array]           [array going through wp filter]
	 */
	public function custom_column($column_name) {
		global $post;
		EM_lists::custom_column($post->ID, BOK, $column_name);
	}


	/**
	 * filter for sorting by columns on ctp list page
	 * 
	 * @param  [array] $defaults [array going through wp filter]
	 * @return [array]           [array going through wp filter]
	 */
	public function sort_column($columns) {
		return EM_lists::sort_column($columns, BOK.'_sort');
	}

	public function set_sort($query) {
		Em_lists::set_sort($query, BOK);
	}

	/*
		creates wordpress metabox
		adds javascript
	*/
	public function create_meta() {
	    wp_enqueue_style('thickbox');
	    wp_enqueue_script('media-upload');
	    wp_enqueue_script('thickbox');

		/* lan info meta */
		add_meta_box(
			BOK.'_meta', // name
			'Bok Info', // title 
			array($this,'create_meta_box'), // callback
			BOK // page
		);

		/* to show or not on front-end */
		add_meta_box(
			BOK.'_exclude',
			'Aldri vis',
			array($this, 'exclude_meta_box'),
			BOK,
			'side'
		);
		
		/* adding admin css and js */
		wp_enqueue_style(BOK.'-admin-style', BOK_PLUGIN_URL . 'assets/css/admin/em-bok.css', array(), '1.0.0');
		wp_enqueue_script(BOK.'-admin', BOK_PLUGIN_URL . 'assets/js/admin/em-bok.js', array(), '1.0.0', true);
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
		EM_list_edit::create_exclude_box($post, BOK);
	}



	/**
	 * wp action when saving
	 */
	public function save($post_id) {
		EM_list_edit::save($post_id, BOK);
	}

}