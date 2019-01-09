<?php 
defined('ABSPATH') or die('Blank Space');


/*
*/
final class Lan_dk_edit {
	/* singleton */
	private static $instance = null;

	private $name = 'emlanlistdk';

	public static function get_instance() {
		if (self::$instance === null) self::$instance = new self();
		return self::$instance;
	}



	private function __construct() {


		add_action('manage_'.$this->name.'_posts_columns', array($this, 'column_head'));
		add_filter('manage_'.$this->name.'_posts_custom_column', array($this, 'custom_column'));
		add_filter('manage_edit-'.$this->name.'_sortable_columns', array($this, 'sort_column'));
		add_action('pre_get_posts', array($this, 'set_sort'));
		
		/* metabox, javascript */
		add_action('add_meta_boxes_'.$this->name, array($this, 'create_meta'));
		/* hook for page saving/updating */
		add_action('save_post', array($this, 'save'));


		add_filter('emtheme_doc', array($this, 'add_doc'), 99);

	}


	/**
	 * wp filter for adding columns on ctp list page
	 * 
	 * @param  [array] $defaults [array going through wp filter]
	 * @return [array]           [array going through wp filter]
	 */
	public function column_head($defaults) {
		return EM_lists::custom_head($defaults, $this->name.'_sort');
	}


	/**
	 * filter for populating columns on ctp list page
	 * 
	 * @param  [array] $defaults [array going through wp filter]
	 * @return [array]           [array going through wp filter]
	 */
	public function custom_column($column_name) {
		global $post;
		EM_lists::custom_column($post->ID, $this->name, $column_name);
	}


	/**
	 * filter for sorting by columns on ctp list page
	 * 
	 * @param  [array] $defaults [array going through wp filter]
	 * @return [array]           [array going through wp filter]
	 */
	public function sort_column($columns) {
		return EM_lists::sort_column($columns, $this->name.'_sort');
	}

	public function set_sort($query) {
		Em_lists::set_sort($query, $this->name);
	}

	/*
		creates wordpress metabox
		adds javascript
	*/
	public function create_meta() {

		/* lan info meta */
		add_meta_box(
			$this->name.'_meta', // name
			'Lån Info', // title 
			array($this,'create_meta_box'), // callback
			$this->name // page
		);

		/* to show or not on front-end */
		add_meta_box(
			$this->name.'_exclude',
			'Aldri vis',
			array($this, 'exclude_meta_box'),
			$this->name,
			'side'
		);
		
		/* adding admin css and js */
		wp_enqueue_style($this->name.'-admin-style', LAN_DK_PLUGIN_URL . 'assets/css/admin/em-lanlist-dk.css', array(), '1.0.0');
		wp_enqueue_script($this->name.'-admin', LAN_DK_PLUGIN_URL . 'assets/js/admin/em-lanlist-dk.js', array(), '1.0.0', true);
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
		EM_list_edit::create_exclude_box($post, $this->name);
	}



	/**
	 * wp action when saving
	 */
	public function save($post_id) {
		EM_list_edit::save($post_id, $this->name);
	}

}