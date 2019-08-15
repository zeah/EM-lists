<?php 
defined('ABSPATH') or die('Blank Space');


/*
*/
final class Matkasse_edit {
	/* singleton */
	private static $instance = null;

	private $name = 'matkasselist';

	public static function get_instance() {
		if (self::$instance === null) self::$instance = new self();
		return self::$instance;
	}



	private function __construct() {
		add_action('manage_'.MAT.'_posts_columns', array($this, 'column_head'));
		add_filter('manage_'.MAT.'_posts_custom_column', array($this, 'custom_column'));
		add_filter('manage_edit-'.MAT.'_sortable_columns', array($this, 'sort_column'));
		add_action('pre_get_posts', array($this, 'set_sort'));

		/* metabox, javascript */
		add_action('add_meta_boxes_'.MAT, array($this, 'create_meta'));
		/* hook for page saving/updating */
		add_action('save_post', array($this, 'save'));

		add_action('admin_enqueue_scripts', [$this, 'admin_sands']);

		add_action('admin_enqueue_scripts', [$this, 'add_js']);
	}


	/**
	 *
	 */
	public function add_js() {
		$id = get_current_screen();
		if ($id->id != 'edit-'.MAT) return;


		$args = [
			'post_type' 		=> MAT,
			'posts_per_page' 	=> -1,
			'orderby'			=> [
										'meta_value_num' => 'ASC',
										'title' => 'ASC'
								   ]
		];

		$posts = get_posts($args);

		$po = [];

		foreach ($posts as $p) $po[$p->ID] = $p->post_name;

		$po['name'] = 'mat';

		$po['tax'] = MAT.'type';

		wp_enqueue_script(MAT.'_column', EM_LISTS_PLUGIN_URL.'assets/js/admin/emlist-column.js', ['jquery'], false, true);

		wp_localize_script(MAT.'_column', 'listdata', json_encode($po));
	}

	public function admin_sands() {
		$id = get_current_screen();
		if ($id->id != MAT) return;

		EM_list_edit::sands();
		wp_enqueue_style('em-'.MAT.'-admin-style', MATKASSELIST_PLUGIN_URL . 'assets/css/admin/em-matkasselist.css', [], '1.0.0');
	}


	/**
	 * wp filter for adding columns on ctp list page
	 * 
	 * @param  [array] $defaults [array going through wp filter]
	 * @return [array]           [array going through wp filter]
	 */
	public function column_head($defaults) {
		return EM_lists::custom_head($defaults, MAT.'_sort');
	}


	/**
	 * filter for populating columns on ctp list page
	 * 
	 * @param  [array] $defaults [array going through wp filter]
	 * @return [array]           [array going through wp filter]
	 */
	public function custom_column($column_name) {
		global $post;
		EM_lists::custom_column($post->ID, MAT, $column_name);
	}


	/**
	 * filter for sorting by columns on ctp list page
	 * 
	 * @param  [array] $defaults [array going through wp filter]
	 * @return [array]           [array going through wp filter]
	 */
	public function sort_column($columns) {
		return EM_lists::sort_column($columns, MAT.'_sort');
	}

	/* telling wp how to sort the meta values */
	public function set_sort($query) {
		Em_lists::set_sort($query, MAT);
	}



	/*
		creates wordpress metabox
		adds javascript
	*/
	public function create_meta() {

		/* lan info meta */
		add_meta_box(
			MAT.'_meta', // name
			'Matkasse Info', // title 
			array($this,'create_meta_box'), // callback
			MAT // page
		);

		/* to show or not on front-end */
		add_meta_box(
			MAT.'_exclude',
			'Aldri vis',
			array($this, 'exclude_meta_box'),
			MAT,
			'side'
		);
		
		/* adding admin css and js */
		// wp_enqueue_style(MAT.'-admin-style', MATKASSELIST_PLUGIN_URL . 'assets/css/admin/em-matkasselist.css', array(), '1.0.0');
		// wp_enqueue_script(MAT.'-admin', MATKASSELIST_PLUGIN_URL . 'assets/js/admin/em-matkasselist.js', array(), '1.0.0', true);
	}


	/*
		creates content in metabox
	*/
	public function create_meta_box($post) {
		$template = [
			'meta' => [
				'readmore' => [ 'title' => 'landingside link (title link/les mer link)' ],
				'bestill' => [ 'title' => 'affiliate link (bestill knapp/logo link)' ],
				'info01' => [ 'title' => 'Listpart #1' ],
				'info02' => [ 'title' => 'Listpart #2' ],
				'info03' => [ 'title' => 'Listpart #3' ],
				'info04' => [ 'title' => 'Infopart #4 (pris)' ],
				'info05' => [ 'title' => 'Infopart #1 (tittel)' ],
				'info06' => [ 'title' => 'Infopart #2 (antall dager)' ],
				'info07' => [ 'title' => 'Infopart #3 (antall personer)' ],
				'info08' => [ 'title' => 'Blurb' ],
				'pixel' => [ 'title' => 'pixel url' ],
				// 'terning' => ['title' => 'terning', 'dropdown' => true]
			],
			'struc' => [
				'bank' => ['title' => 'bank name']
			],
			'sort' => [MAT.'_sort']
		];		

		EM_list_edit::create_meta_box($post, $post->post_type, $template);
	}
 

 	/**
 	 * [exclude_meta_box description]
 	 */
	public function exclude_meta_box() {
		global $post;
		EM_list_edit::create_exclude_box($post, MAT);
	}


	/**
	 * wp action when saving
	 */
	public function save($post_id) {
		EM_list_edit::save($post_id, MAT);
	}
}