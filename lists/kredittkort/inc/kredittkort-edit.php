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

		add_action('admin_enqueue_scripts', array($this, 'admin_sands'));

		add_action('manage_'.KREDITTKORT.'_posts_columns', [$this, 'column_head']);
		add_filter('manage_'.KREDITTKORT.'_posts_custom_column', [$this, 'custom_column']);
		add_filter('manage_edit-'.KREDITTKORT.'_sortable_columns', [$this, 'sort_column']);
		
		/* metabox, javascript */
		add_action('add_meta_boxes_'.KREDITTKORT, [$this, 'create_meta']);
		/* hook for page saving/updating */
		add_action('save_post', [$this, 'save']);


		// add_filter('emtheme_doc', array($this, 'add_doc'), 99);
		add_action('admin_enqueue_scripts', [$this, 'add_js']);


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

		// wp_die('<xmp>'.print_r($id, true).'</xmp>');
		


		wp_enqueue_script(KREDITTKORT.'_column', EM_LISTS_PLUGIN_URL.'assets/js/admin/emlist-column.js', ['jquery'], false, true);

		wp_localize_script(KREDITTKORT.'_column', 'listdata', json_encode($po));
	}


	/**
	 *
	 */
	public function admin_sands() {
		$id = get_current_screen();
		// wp_die('<xmp>'.print_r($id, true).'</xmp>');
		if ($id->id != KREDITTKORT) return;

		EM_list_edit::sands();
		wp_enqueue_style('em-'.KREDITTKORT.'-admin-style', KREDITTKORT_PLUGIN_URL . 'assets/css/admin/em-kredittkort.css', array(), '1.0.1');
	}

	/**
	 * theme filter for populating documentation
	 * 	
	 * @param [array] $data [array passing through theme filter]
	 */
	// public function add_doc($data) {
	// 	$data['kredittkort']['title'] = '<h1 id="kredittkort">Kredittkort (Plugin)</h1>';

	// 	$data['kredittkort']['index'] = '<li><h2><a href="#kredittkort">Kredittkort (Plugin)</a></h2>
	// 										<ul>
	// 											<li><a href="#kredittkort-shortcode">Shortcode</a></li>
	// 											<li><a href="#kredittkort-aldri">Aldri vis</a></li>
	// 											<li><a href="#kredittkort-sort">Sorting order</a></li>
	// 											<li><a href="#kredittkort-overview">Overview</a></li>
	// 										</ul>
	// 									</li>';
	// 	$data['kredittkort']['info'] = '<li id="kredittkort-shortcode"><h2>Shortcodes</h2>
	// 									<ul>
	// 										<li><b>[kredittkort]</b>
	// 										<p>[kredittkort] will show all.</p>
	// 										</li>
	// 										<li><b>[kredittkort name="xx, yy"]</b>
	// 										<p>Shows only the creditcards that is mentioned in the shortcode.
	// 										<br>The name needs to be the slug-name of the creditcard.
	// 										<br>Loans are sorted by the position they have in name=""
	// 										<br>eks.: [kredittkort name="lendo-kort"] will only show the creditcard with slug-name "lendo-kort"
	// 										<br>[kredittkort name="lendo-privatlan, axo-finans"] will show 2 loans: lendo and axo.</p>
	// 										<li><b>[kredittkort kredittkort="xx"]</b>
	// 										<p>kredittkort must match the slug-name of the kredittkort type.
	// 										<br>The loans are sorted by the sort order given in load edit page for that type.
	// 										<br>Eks: [kredittkort kredittkort="frontpage"] shows all loans with the category "frontpage" in the order of lowest number
	// 										<br>of field "Sort frontpage" has in the load editor page.</p>
	// 										</li>
	// 										</li>
	// 										<li><b>[kredittkort-bilde name="xx"]</b>
	// 										<p>Name is required. Will show the creditcard\'s thumbnail with a link.
	// 										<br>[kredittkort-bestill name="xx" source="test"] will append &source=test at the link.</p></li>
	// 										<li><b>[kredittkort-bestill name="xx"]</b>
	// 										<p>Name is required. Will show the creditcard\'s button.
	// 										<br>[kredittkort-bestill name="xx" source="test"] will append &source=test at the link.</p></li>
	// 									</ul>
	// 									</li>
	// 									<li id="kredittkort-aldri"><h2>Aldri vis</h2>
	// 									<p>If tagged, then the creditcard will never appear on the front-end.</p>
	// 									</li>
	// 									</li>
	// 									<li id="kredittkort-sort"><h2>Sorting order</h2>
	// 									<p>The loans will be shown with the lowest "Sort"-value first.
	// 									<br>When only showing a specific category on creditcard page, then the sort order column will reflect 
	// 									<br>that category\'s sort order.</p>
	// 									</li>
	// 									<li id="kredittkort-overview"><h2>Overview</h2>
	// 									<p> The <a target="_blank" href="'.get_site_url().'/wp-admin/edit.php?post_type=kredittkort&page=kredittkort-overview">overview page</a> will show every post and page and whether or not there are
	// 									<br>any lan shortcodes in them.
	// 									<br>You can sort the columns alphabetically</p>
	// 									</li>
	// 									';

	// 	return $data;
	// }

	/**
	 * wp filter for adding columns on ctp list page
	 * 
	 * @param  [array] $defaults [array going through wp filter]
	 * @return [array]           [array going through wp filter]
	 */
	// public function column_head($defaults) {
	// 	$defaults[KREDITTKORT.'_sort'] = 'Sorting Order';
	// 	return $defaults;
	// }
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

	// public function custom_column($column_name) {
	// 	global $post;
	// 	// echo $_SERVER['QUERY_STRING'];

	// 	// echo parse_url()
		
	// 	// echo print_r($q_out, true);

	// 	if ($column_name == KREDITTKORT.'_sort') {
	// 		$q_out = null;
	// 		parse_str($_SERVER['QUERY_STRING'], $q_out);

	// 		$meta = KREDITTKORT.'_sort';
	// 		if (isset($q_out[KREDITTKORT.'type'])) $meta = $meta.'_'.$q_out[KREDITTKORT.'type'];

	// 		$meta = get_post_meta($post->ID, $meta);
			
	// 		if (isset($meta[0])) echo $meta[0];
	// 	}
	// }


	/**
	 * filter for sorting by columns on ctp list page
	 * 
	 * @param  [array] $defaults [array going through wp filter]
	 * @return [array]           [array going through wp filter]
	 */
	// public function sort_column($columns) {
	// 	$columns[KREDITTKORT.'_sort'] = KREDITTKORT.'_sort';
	// 	return $columns;
	// }
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
		
		/* adding admin css and js */
		// wp_enqueue_style('em-kredittkort-admin-style', KREDITTKORT_PLUGIN_URL . 'assets/css/admin/em-kredittkort.css', array(), '1.0.1');
		// wp_enqueue_script(KREDITTKORT.'-admin', KREDITTKORT_PLUGIN_URL . 'assets/js/admin/em-kredittkort.js', array(), '1.0.1', true);
	}


	/*
		creates content in metabox
	*/
	public function create_meta_box($post) {

		$template = [
			'meta' => [
				'readmore' => [ 'title' => 'landingside link (title link/les mer link)' ],
				'bestill' => [ 'title' => 'affiliate link (bestill knapp/logo link)' ],
				'info01' => [ 'title' => 'info01' ],
				'info02' => [ 'title' => 'info02' ],
				'info03' => [ 'title' => 'info03' ],
				'info04' => [ 'title' => 'info04' ],
				'info05' => [ 'title' => 'info05' ],
				'info06' => [ 'title' => 'info06' ],
				'info07' => [ 'title' => 'info07' ],
				'info08' => [ 'title' => 'info08' ],
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