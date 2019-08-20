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
		
		add_action('admin_enqueue_scripts', [$this, 'admin_sands']);

		add_action('admin_enqueue_scripts', [$this, 'add_js']);
	}

	public function admin_sands() {
		$id = get_current_screen();
		if ($id->id != EMLAN) return;

		EM_list_edit::sands();
		wp_enqueue_style('em-'.EMLAN.'-admin-style', LANLIST_PLUGIN_URL . 'assets/css/admin/em-lanlist.css', [], '1.0.0');
	}


	public function add_js() {
		$id = get_current_screen();
		if ($id->id != 'edit-'.EMLAN) return;


		$args = [
			'post_type' 		=> EMLAN,
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

		$po['tax'] = EMLAN.'type';

		wp_enqueue_script(EMLAN.'_column', EM_LISTS_PLUGIN_URL.'assets/js/admin/emlist-column.js', ['jquery'], false, true);

		wp_localize_script(EMLAN.'_column', 'listdata', json_encode($po));
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
		// wp_enqueue_style(EMLAN.'-admin-style', LANLIST_PLUGIN_URL . 'assets/css/admin/em-lanlist.css', array(), '1.0.3');
		// wp_enqueue_script(EMLAN.'-admin', LANLIST_PLUGIN_URL . 'assets/js/admin/em-lanlist.js', array(), '1.0.4', true);
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
				'info04' => [ 'title' => 'Infopart #4 (renter)' ],
				'info05' => [ 'title' => 'Infopart #1 (lånebeløp)' ],
				'info06' => [ 'title' => 'Infopart #2 (nedbetalingstid)' ],
				'info07' => [ 'title' => 'Infopart #3 (aldersgrense)' ],
				'info08' => [ 'title' => 'Eff. rente eksempel' ],
				// 'info01' => [ 'title' => 'info01' ],
				// 'info02' => [ 'title' => 'info02' ],
				// 'info03' => [ 'title' => 'info03' ],
				// 'info04' => [ 'title' => 'info04' ],
				// 'info05' => [ 'title' => 'info05' ],
				// 'info06' => [ 'title' => 'info06' ],
				// 'info07' => [ 'title' => 'info07' ],
				// 'info08' => [ 'title' => 'info08' ],
				'pixel' => [ 'title' => 'pixel url' ]
			],
			'struc' => [
				'brand_name' => ['title' => 'brand name (bank)'],
				'brand_url' => ['title' => 'url to brand (bank)'],
				'same_as' => ['title' => 'url to page which is same as (ex. url to landingpage of specific credit card on the brand site)'],
				'amount_max_value' => ['title' => 'max credit limit'],
				'amount_min_value' => ['title' => 'min credit limit'],
				'amount_currency' => ['title' => 'currency of loan'],
				'interestrate_min_value' => ['title' => 'lowest interest rate'],
				'interestrate_max_value' => ['title' => 'highest interest rate'],
				'loanterm_min_value' => ['title' => 'min duration of loan'],
				'loanterm_max_value' => ['title' => 'max duration of loan']
				// 'grace_period' => ['title' => 'grace period (rentefri dager)']
			],
			'sort' => [EMLAN.'_sort']
		];
		EM_list_edit::create_meta_box($post, $post->post_type, $template);
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