<?php 
defined('ABSPATH') or die('Blank Space');


/*
*/
final class Kredittkort_edit {
	/* singleton */
	private static $instance = null;


	public static function get_instance() {
		if (self::$instance === null) self::$instance = new self();
		return self::$instance;
	}



	private function __construct() {

		// sands for edit/new page
		add_action('admin_enqueue_scripts', array($this, 'admin_sands'));

		// sands for column page (js for creating shortcode)
		add_action('admin_enqueue_scripts', [$this, 'add_js']);

		// adding and making new column sortable
		add_action('manage_'.KREDITTKORT.'_posts_columns', [$this, 'column_head']);
		add_filter('manage_'.KREDITTKORT.'_posts_custom_column', [$this, 'custom_column']);
		add_filter('manage_edit-'.KREDITTKORT.'_sortable_columns', [$this, 'sort_column']);
		
		/* metabox, javascript */
		add_action('add_meta_boxes_'.KREDITTKORT, [$this, 'create_meta']);
		/* hook for page saving/updating */
		add_action('save_post', [$this, 'save']);


		// add_filter('emtheme_doc', array($this, 'add_doc'), 99);
	}


	/**
	 *
	 */
	public function add_js() {
		$id = get_current_screen();
		if ($id->id != 'edit-'.KREDITTKORT) return;


		$args = [
			'post_type' 		=> KREDITTKORT,
			'posts_per_page' 	=> -1,
			'orderby'			=> [
										'meta_value_num' => 'ASC',
										'title' => 'ASC'
								   ]
		];

		$posts = get_posts($args);

		$po = [];

		foreach ($posts as $p) $po[$p->ID] = $p->post_name;

		$po['name'] = 'kredittkort';

		$po['tax'] = KREDITTKORT.'type';


		wp_enqueue_script(KREDITTKORT.'_column', EM_LISTS_PLUGIN_URL.'assets/js/admin/emlist-column.js', ['jquery'], false, true);

		wp_localize_script(KREDITTKORT.'_column', 'listdata', json_encode($po));
	}


	/**
	 *
	 */
	public function admin_sands() {
		$id = get_current_screen();
		if ($id->id != KREDITTKORT) return;

		EM_list_edit::sands();
		wp_enqueue_style('em-'.KREDITTKORT.'-admin-style', KREDITTKORT_PLUGIN_URL . 'assets/css/admin/em-kredittkort.css', array(), '1.0.1');
	}

	

	/**
	 * wp filter for adding columns on ctp list page
	 * 
	 * @param  [array] $defaults [array going through wp filter]
	 * @return [array]           [array going through wp filter]
	 */
	public function column_head($defaults) {
		return EM_lists::custom_head($defaults, KREDITTKORT.'_sort');
	}

	/**
	 * filter for populating columns on ctp list page
	 * 
	 * @param  [array] $defaults [array going through wp filter]
	 * @return [array]           [array going through wp filter]
	 */

	public function custom_column($column_name) {
		global $post;
		EM_lists::custom_column($post->ID, KREDITTKORT, $column_name);
	}




	/**
	 * filter for sorting by columns on ctp list page
	 * 
	 * @param  [array] $defaults [array going through wp filter]
	 * @return [array]           [array going through wp filter]
	 */
	public function sort_column($columns) {
		return EM_lists::sort_column($columns, KREDITTKORT.'_sort');
	}

	public function set_sort($query) {
		Em_lists::set_sort($query, KREDITTKORT);
	}


	/*
		creates wordpress metabox
		adds javascript
	*/
	public function create_meta() {

		/* lan info meta */
		add_meta_box(
			KREDITTKORT.'_meta', // name
			'Kreditt Info', // title 
			array($this,'create_meta_box'), // callback
			KREDITTKORT // page
		);

		/* to show or not on front-end */
		add_meta_box(
			KREDITTKORT.'_exclude',
			'Aldri vis',
			array($this, 'exclude_meta_box'),
			KREDITTKORT,
			'side'
		);
		
	}


	/*
		creates content in metabox
	*/
	public function create_meta_box($post) {

		$template = [
			'meta' => [
				'readmore' => [ 'title' => 'landingside link (title link/les mer link)' ],
				'bestill' => [ 'title' => 'affiliate link (bestill knapp/logo link)' ],
				'info01' => [ 'title' => 'Listpunkt #1' ],
				'info02' => [ 'title' => 'Listpunkt #2' ],
				'info03' => [ 'title' => 'Listpunkt #3' ],
				'info04' => [ 'title' => 'Blurb' ],
				'info05' => [ 'title' => 'Infopunkt #2 (maks kreditt)' ],
				'info06' => [ 'title' => 'Infopunkt #3 (rentefri kreditt)' ],
				'info07' => [ 'title' => 'Infopunkt #1 (aldersgrense)' ],
				'info08' => [ 'title' => 'Eff. rente eksempel' ],
				'pixel' => [ 'title' => 'pixel url' ],
			],
			'struc' => [
				'bank' => ['title' => 'bank name']
			],
			'sort' => [KREDITTKORT.'_sort']
		];

		EM_list_edit::create_meta_box($post, $post->post_type, $template);
	}

 

 	/**
 	 * [exclude_meta_box description]
 	 */
 	public function exclude_meta_box() {
		global $post;
		EM_list_edit::create_exclude_box($post, KREDITTKORT);
	}


	/**
	 * wp action when saving
	 */
	public function save($post_id) {
		EM_list_edit::save($post_id, KREDITTKORT);
	}
}