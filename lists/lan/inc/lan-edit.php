<?php 
defined('ABSPATH') or die('Blank Space');


/*
*/
final class Lan_edit {
	/* singleton */
	private static $instance = null;


	public static function get_instance() {
		if (self::$instance === null) self::$instance = new self();
		return self::$instance;
	}



	private function __construct() {


		add_action('manage_emlanlist_posts_columns', array($this, 'column_head'));
		add_filter('manage_emlanlist_posts_custom_column', array($this, 'custom_column'));
		add_filter('manage_edit-emlanlist_sortable_columns', array($this, 'sort_column'));
		add_action('pre_get_posts', array($this, 'set_sort'));

		/* metabox, javascript */
		add_action('add_meta_boxes_emlanlist', array($this, 'create_meta'));
		/* hook for page saving/updating */
		add_action('save_post', array($this, 'save'));


		add_filter('emtheme_doc', array($this, 'add_doc'), 99);

	}

	/**
	 * theme filter for populating documentation
	 * 	
	 * @param [array] $data [array passing through theme filter]
	 */
	public function add_doc($data) {
		$data['emlanlist']['title'] = '<h1 id="emlanlist">Lånlist (Plugin)</h1>';

		$data['emlanlist']['index'] = '<li><h2><a href="#emlanlist">Lånlist (Plugin)</a></h2>
											<ul>
												<li><a href="#emlanlist-shortcode">Shortcode</a></li>
												<li><a href="#emlanlist-aldri">Aldri vis</a></li>
												<li><a href="#emlanlist-sort">Sorting order</a></li>
												<li><a href="#emlanlist-overview">Overview</a></li>
											</ul>
										</li>';
		$data['emlanlist']['info'] = '<li id="emlanlist-shortcode"><h2>Shortcodes</h2>
										<ul>
											<li><b>[lan]</b>
											<p>[lan] will show all.</p>
											</li>
											<li><b>[lan name="xx, yy"]</b>
											<p>Shows only the loans that is mentioned in the shortcode.
											<br>The name needs to be the slug-name of the loan.
											<br>Loans are sorted by the position they have in name=""
											<br>eks.: [lan name="lendo-privatlan"] will only show the loan with slug-name "lendo-privatlån.
											<br>[lan name="lendo-privatlan, axo-finans"] will show 2 loans: lendo and axo.</p>
											<li><b>[lan lan="xx"]</b>
											<p>lan must match the slug-name of the lan type.
											<br>The loans are sorted by the sort order given in load edit page for that type.
											<br>Eks: [lan lan="frontpage"] shows all loans with the category "frontpage" in the order of lowest number
											<br>of field "Sort frontpage" has in the load editor page.</p>
											</li>
											</li>
											<li><b>[lan-bilde name="xx"]</b>
											<p>Name is required. Will show the loan\'s thumbnail with a link.
											<br>[kredittkort-bestlil name="xx" source="test"] will append &source=test at the link.</p></li>
											<li><b>[lan-bestill name="xx"]</b>
											<p>Name is required. Will show the loan\'s button.
											<br>[kredittkort-bestlil name="xx" source="test"] will append &source=test at the link.</p></li>
											
										</ul>
										</li>
										<li id="emlanlist-aldri"><h2>Aldri vis</h2>
										<p>If tagged, then the loan will never appear on the front-end.</p>
										</li>
										</li>
										<li id="emlanlist-sort"><h2>Sorting order</h2>
										<p>The loans will be shown with the lowest "Sort"-value first.
										<br>When only showing a specific category on loan page, then the sort order column will reflect 
										<br>that category\'s sort order.</p>
										</li>
										<li id="emlanlist-overview"><h2>Overview</h2>
										<p> The <a target="_blank" href="'.get_site_url().'/wp-admin/edit.php?post_type=emlanlist&page=emlanlist-overview">overview page</a> will show every post and page and whether or not there are
										<br>any lan shortcodes in them.
										<br>You can sort the columns alphabetically</p>
										</li>
										';

		return $data;
	}

	/**
	 * wp filter for adding columns on ctp list page
	 * 
	 * @param  [array] $defaults [array going through wp filter]
	 * @return [array]           [array going through wp filter]
	 */
	public function column_head($defaults) {
		return EM_lists::custom_head($defaults, 'emlanlist_sort');
	}


	/**
	 * filter for populating columns on ctp list page
	 * 
	 * @param  [array] $defaults [array going through wp filter]
	 * @return [array]           [array going through wp filter]
	 */
	public function custom_column($column_name) {
		global $post;
		EM_lists::custom_column($post->ID, 'emlanlist', $column_name);
	}


	/**
	 * filter for sorting by columns on ctp list page
	 * 
	 * @param  [array] $defaults [array going through wp filter]
	 * @return [array]           [array going through wp filter]
	 */
	public function sort_column($columns) {
		return EM_lists::sort_column($columns, 'emlanlist_sort');
	}

	/* telling wp how to sort the meta values */
	public function set_sort($query) {
		Em_lists::set_sort($query, 'emlanlist');
	}



	/*
		creates wordpress metabox
		adds javascript
	*/
	public function create_meta() {

		/* lan info meta */
		add_meta_box(
			'emlanlist_meta', // name
			'Lån Info', // title 
			array($this,'create_meta_box'), // callback
			'emlanlist' // page
		);

		/* to show or not on front-end */
		add_meta_box(
			'emlan_exclude',
			'Aldri vis',
			array($this, 'exclude_meta_box'),
			'emlanlist',
			'side'
		);
		
		/* adding admin css and js */
		wp_enqueue_style('emlanlist-admin-style', LANLIST_PLUGIN_URL . 'assets/css/admin/em-lanlist.css', array(), '1.0.2');
		wp_enqueue_script('emlanlist-admin', LANLIST_PLUGIN_URL . 'assets/js/admin/em-lanlist.js', array(), '1.0.3', true);
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
		EM_list_edit::create_exclude_box($post, 'emlanlist');
	}


	/**
	 * wp action when saving
	 */
	public function save($post_id) {
		EM_list_edit::save($post_id, 'emlanlist');
	}
}