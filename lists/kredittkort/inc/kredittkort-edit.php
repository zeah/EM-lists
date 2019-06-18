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

		$po['name'] = 'lan';

		$po['tax'] = KREDITTKORT.'type';

		wp_enqueue_script(KREDITTKORT.'_column', EM_LISTS_PLUGIN_URL.'assets/js/admin/emlist-column.js', ['jquery'], false, true);

		wp_localize_script(KREDITTKORT.'_column', 'listdata', json_encode($po));
	}


	/**
	 *
	 */
	public function admin_sands() {
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
		wp_enqueue_script(KREDITTKORT.'-admin', KREDITTKORT_PLUGIN_URL . 'assets/js/admin/em-kredittkort.js', array(), '1.0.1', true);
	}


	/*
		creates content in metabox
	*/

	public function create_meta_box($post) {
		EM_list_edit::create_meta_box($post, $post->post_type);
	}

	// public function create_meta_box($post) {
	// 	wp_nonce_field('em'.basename(__FILE__), KREDITTKORT.'_nonce');

	// 	$meta = get_post_meta($post->ID, 'kredittkort_data');
	// 	$sort = get_post_meta($post->ID, 'kredittkort_sort');

	// 	$tax = wp_get_post_terms($post->ID, 'kredittkorttype');

	// 	$taxes = [];
	// 	if (is_array($tax))
	// 		foreach($tax as $t)
	// 			array_push($taxes, $t->slug);

	// 	$json = [
	// 		'meta' => isset($meta[0]) ? $this->sanitize($meta[0]) : '',
	// 		'kredittkort_sort' => isset($sort[0]) ? floatval($sort[0]) : '',
	// 		'tax'  => $taxes
	// 	];

	// 	$ameta = get_post_meta($post->ID);
	// 	foreach($ameta as $key => $value)
	// 		if (strpos($key, 'kredittkort_sort_') !== false && isset($value[0])) $json[$key] = esc_html($value[0]);


	// 	wp_localize_script('em-kredittkort-admin', 'kredittkort_meta', json_decode(json_encode($json), true));
	// 	echo '<div class="kredittkort-meta-container"></div>';
	// }
 

 	/**
 	 * [exclude_meta_box description]
 	 */

 	public function exclude_meta_box() {
		global $post;
		EM_list_edit::create_exclude_box($post, KREDITTKORT);
	}
	// public function exclude_meta_box() {
	// 	global $post;

	// 	$exclude = get_option('kredittkort_exclude');
	// 	if (!is_array($exclude)) $exclude = [];

	// 	$exclude_serp = get_option('kredittkort_exclude_serp');
	// 	if (!is_array($exclude_serp)) $exclude_serp = [];

	// 	echo '<p><input name="kredittkort_exclude" id="kredittkort_exc" type="checkbox"'.(array_search($post->ID, $exclude) !== false ? ' checked' : '').'><label for="kredittkort_exc">Kortet vil ikke vises på front-end når boksen er markert.</label></p>
	// 	      <p><input name="kredittkort_exclude_serp" id="kredittkort_exc_serp" type="checkbox"'.(array_search($post->ID, $exclude_serp) !== false ? ' checked' : '').'><label for="kredittkort_exc_serp">Ikke hvis i interal SERP.</label></p>';
	// }



	/**
	 * wp action when saving
	 */

	public function save($post_id) {
		EM_list_edit::save($post_id, KREDITTKORT);
	}

	/**
	 * [updating wp option]
	 * @param  [type] $data  [var to be saved to]
	 * @param  [type] $value [data to be saved]
	 */
	// private function u_option($data, $value) {
	// 	$option = get_option($data);
	// 	if (!is_array($option)) $option = []; // to avoid php error
		
	// 	$value = intval($value);

	// 	if (isset($_POST[$data])) {

	// 		// if not already added
	// 		if (array_search($value, $option) === false) {

	// 			// if to add to collection
	// 			if (is_array($option)) {
	// 				array_push($option, $value);
	// 				update_option($data, $option);
	// 			}
				
	// 			// if to create collection (of one)
	// 			else update_option($data, [$value]);
	// 		}
	// 	}
	// 	// when removing
	// 	else {
	// 		// $option = get_option($data);

	// 		if (array_search($value, $option) !== false) {
	// 			unset($option[array_search($value, $option)]);
	// 			update_option($data, $option);
	// 		}
	// 	}
	// }

	/*
		recursive sanitizer
	*/
	// private function sanitize($data) {
	// 	if (!is_array($data)) return wp_kses_post($data);

	// 	$d = [];
	// 	foreach($data as $key => $value) {
	// 		switch ($key) {
	// 			case 'bestill':
	// 			case 'pixel':
	// 			case 'readmore': $d[$key] = sanitize_text_field($value); break;
	// 			default: $d[$key] = $this->sanitize($value); break;
	// 		}
	// 	}

	// 	return $d;
	// }
}