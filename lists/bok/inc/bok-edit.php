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
		add_action('add_meta_boxes_'.BOK.'liste', [$this, 'create_meta']);
		/* hook for page saving/updating */
		add_action('save_post', [$this, 'save']);

		add_action('admin_enqueue_scripts', [$this, 'admin_sands']);

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

	public function admin_sands() {
		$id = get_current_screen();
		if ($id->id != BOK.'liste') return;
		// wp_die('<xmp>'.print_r($id, true).'</xmp>');
		EM_list_edit::sands();
		wp_enqueue_style('em-'.BOK.'-admin-style', BOK_PLUGIN_URL . 'assets/css/admin/em-bok.css', [], '1.0.0');
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


	    // wp_die('<xmp>'.print_r('hi', true).'</xmp>');
		/* lan info meta */
		add_meta_box(
			BOK.'_meta', // name
			'Bok Info', // title 
			array($this,'create_meta_box'), // callback
			BOK.'liste' // page
		);

		/* to show or not on front-end */
		add_meta_box(
			BOK.'_exclude',
			'Aldri vis',
			array($this, 'exclude_meta_box'),
			BOK.'liste',
			'side'
		);
		
		/* adding admin css and js */
		// wp_enqueue_style(BOK.'-admin-style', BOK_PLUGIN_URL . 'assets/css/admin/em-bok.css', array(), '1.0.0');
		// wp_enqueue_script(BOK.'-admin', BOK_PLUGIN_URL . 'assets/js/admin/em-bok.js', array(), '1.0.0', true);
	}


	/*
		creates content in metabox
	*/
	public function create_meta_box($post) {
		$template = [
			'meta' => [
				'readmore' => [ 'title' => 'landingside link (title link/les mer link)' ],
				'bestill' => [ 'title' => 'affiliate link (bestill knapp/logo link)' ],
				'ctitle' => [ 'title' => 'Custom title' ],
				'info02' => [ 'title' => 'Tekst' ],
				'info03' => [ 'title' => 'Verdi' ],
				'image' => [ 'title' => 'image', 'image' => true ],
				// 'info05' => [ 'title' => 'info05' ],
				// 'info06' => [ 'title' => 'info06' ],
				// 'info07' => [ 'title' => 'info07' ],
				// 'info08' => [ 'title' => 'info08' ],
				'pixel' => [ 'title' => 'pixel url' ],
				// 'terning' => ['title' => 'terning', 'dropdown' => true]
			],
			'struc' => [
				'bank' => ['title' => 'bank name']
			],
			'sort' => [BOK.'_sort']
		];

		EM_list_edit::create_meta_box($post, $post->post_type, $template);
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
		EM_list_edit::save($post_id, BOK.'liste');
	}

}