<?php 

/**
 * WP Shortcodes
 */
final class Lan_se_shortcode {
	/* singleton */
	private static $instance = null;

	private $name = 'emlanlistse';
	private $pf = '-get';
	// public $pixels = [];

	public static function get_instance() {
		if (self::$instance === null) self::$instance = new self();

		return self::$instance;
	}

	private function __construct() {
		$pf = get_option('em_lists');
		if (isset($pf['redir_pf']) && $pf['redir_pf']) $this->pf = '-'.ltrim($pf['redir_pf'], '-');

		$this->wp_hooks();
	}


	/**
	 * hooks for wp
	 */
	private function wp_hooks() {

		// loan list
		if (!shortcode_exists('lan')) add_shortcode('lan', array($this, 'add_shortcode'));
		else add_shortcode('emlanlist', array($this, 'add_shortcode'));

		// loan thumbnail
		if (!shortcode_exists('lan-bilde')) add_shortcode('lan-bilde', array($this, 'add_shortcode_bilde'));
		else add_shortcode('emlanlist-bilde', array($this, 'add_shortcode_bilde'));

		// loan button
		if (!shortcode_exists('lan-bestill')) add_shortcode('lan-bestill', array($this, 'add_shortcode_bestill'));
		else add_shortcode('emlanlist-bestill', array($this, 'add_shortcode_bestill'));

		// button and clickable logo
		if (!shortcode_exists('lan-landingside')) add_shortcode('lan-landingside', array($this, 'add_shortcode_landingside'));
		else add_shortcode('emlanlist-landingside', array($this, 'add_shortcode_landingside'));


		add_filter('search_first', array($this, 'add_serp'));
	}

	/**
	 * adding js for google analytics
	 */
	public function add_inline_script() {
		global $post;
		printf('<script>
			var eles = document.querySelectorAll(".emlanlist-link");
			for (var i = 0; i < eles.length; i++) 
				eles[i].addEventListener("click", function(e) {
					try {
						if ("ga" in window) {
						    tracker = ga.getAll()[0];
						    if (tracker) tracker.send("event", "List Plugin", "%s", this.getAttribute("data-name"), 0);
						}
					}
				
					catch (e) { console.log("ga failed") }
				});
		</script>', $post->post_name);
	}


	/**
	 * returns a list of loans
	 */
	public function add_shortcode($atts, $content = null) {
		add_action('wp_enqueue_scripts', array($this, 'add_css'));
		add_action('wp_footer', ['EM_list_parts', 'add_ga'], 0);

		return $this->get_html(EM_list_sc::posts('emlanlistse', 'lan', $atts, $content), $atts);
	}


	/**
	 * returns only thumbnail from loan
	 */
	public function add_shortcode_bilde($atts, $content = null) {
		if (!isset($atts['name']) || $atts['name'] == '') return;

		add_action('wp_enqueue_scripts', [$this, 'add_css']);
		add_action('wp_footer', ['EM_list_parts', 'add_ga'], 0);
		// add_action('wp_footer', [$this, 'add_inline_script'], 0);

		return EM_list_parts::logo([
				'image' => wp_kses_post(get_the_post_thumbnail_url(EM_list_parts::gp($atts['name'], 'emlanlistse'),'post-thumbnail')),
				'title' => 'Ansök här!',
				'name' => 'emlanlistse'
			]);
	}


	/**
	 * returns bestill button only from loan
	 */
	public function add_shortcode_bestill($atts, $content = null) {
		return EM_list_parts::sc_button($atts, 'emlanlistse', 'Ansök Här!', $this, 'add_css');
	}


	/**
	 * adding sands to head
	 */
	public function add_css() {
        wp_enqueue_style($this->name.'-style', LAN_SE_PLUGIN_URL.'assets/css/pub/em-lanlist-se.css', array(), '1.0.2', '(min-width: 801px)');
        wp_enqueue_style($this->name.'-mobile', LAN_SE_PLUGIN_URL.'assets/css/pub/em-lanlist-se-mobile.css', array(), '1.0.2', '(max-width: 800px)');
	}


	/**
	 * returns the html of a list of loans
	 * @param  WP_Post $posts a wp post object
	 * @return [html]        html list of loans
	 */
	private function get_html($posts, $atts = null) {
		$html = '<ul class="emlanlist-ul">';


		// $parts = EM_list_parts::get_instance();

		foreach ($posts as $p) {
			$meta = get_post_meta($p->ID, $this->name.'_data');

			// skip if no meta found
			if (isset($meta[0])) $meta = $meta[0];
			else continue;

			$meta['post_name'] = $p->post_name;

			// $redir = get_post_meta($p->ID, $this->name.'_redirect');
			// if (isset($redir[0]) && $redir[0]) $redir = true;
			// else $redir = false;

			// grid container
			$html .= sprintf(
				'<li class="emlanlist-list"><form class="emlanlist-container" target="_blank" rel=nofollow action="%s" method="get">', 
				preg_replace('/\?.*$/', '', $meta['bestill']));
			
			// if ($redir) $meta['bestill'] = EM_list_sc::add_site($p->post_name.$this->pf);

			// if (isset($meta['qstring']) && $meta['qstring']) { 
			// 	if ($meta['pixel']) $html .= EM_list_tracking::pixel($meta['pixel'], $meta['ttemplate']);
			// 	$meta['bestill'] = EM_list_tracking::query($meta['bestill'], $meta['ttemplate']);
			// }

			// sanitize meta
			$meta = $this->esc_kses($meta);

			// title
			$html .= '<div class="emlanlist-title-container"><a class="emlanlist-title" href="'.$meta['readmore'].'">'.wp_kses_post($p->post_title).'</a></div>';

			// $test = wp_get_attachment_image_src(get_the_post_thumbnail_url($p,'post-thumbnail'));
 			// $img = wp_get_attachment_image_src( get_post_thumbnail_id($p->ID), "post-thumbnail" );

			// wp_die('<xmp>'.print_r($img, true).'</xmp>');

			// thumbnail
			$html .= EM_list_parts::logo([
				'image' => wp_kses_post(get_the_post_thumbnail_url($p,'post-thumbnail')),
				'meta' => $meta,
				'title' => 'Ansök här!',
				'name' => 'emlanlistse'

			]);
			// $html .= '<div class="emlanlist-logo-container"><a target="_blank" rel="noopener" href="'.$meta['bestill'].'"><img class="emlanlist-logo" src="'.wp_kses_post(get_the_post_thumbnail_url($p,'post-thumbnail')).'"></a></div>';

			// info container
			$html .= '<div class="emlanlist-info-container">';

			// info 1
			if ($meta['info01']) $html .= '<div class="emlanlist-info emlanlist-info-en">'.$meta['info01'].'</div>';

			// info 2
			if ($meta['info02']) $html .= '<div class="emlanlist-info emlanlist-info-to">'.$meta['info02'].'</div>';

			// info 3
			if ($meta['info03']) $html .= '<div class="emlanlist-info emlanlist-info-tre">'.$meta['info03'].'</div>';

			// info 4
			if ($meta['info04']) $html .= '<div class="emlanlist-info emlanlist-info-fire">'.$meta['info04'].'</div>';

			$html .= '</div>';

			// info list container 
			$html .= '<div class="emlanlist-list-container">';

			// info 5
			if ($meta['info05']) $html .= '<div class="emlanlist-info emlanlist-info-fem">'.$meta['info05'].'</div>';

			// info 6
			if ($meta['info06']) $html .= '<div class="emlanlist-info emlanlist-info-seks">'.$meta['info06'].'</div>';

			// info 7
			if ($meta['info07']) $html .= '<div class="emlanlist-info emlanlist-info-syv">'.$meta['info07'].'</div>';

			// info 8
			if ($meta['info08']) $html .= '<div class="emlanlist-info emlanlist-info-atte">'.$meta['info08'].'</div>';

			$html .= '</div>';

			$html .= '<div class="emlanlist-end-container">';
			// terning
			if ($meta['terning'] != 'ingen') {
				$html .= '<svg class="emlanlist-terning">
							<defs>
							    <linearGradient id="emlanlist-grad" x1="0%" y1="100%" x2="100%" y2="100%">
							      <stop offset="0%" style="stop-color:rgb(200,0,0);stop-opacity:1" />
							      <stop offset="100%" style="stop-color:rgb(255,0,0);stop-opacity:1" />
							    </linearGradient>
							  </defs>
							<rect class="emlanlist-rect-svg" rx="7" ry="7" fill="url(#emlanlist-grad)"/>';

				switch ($meta['terning']) {

					case 'seks':
					$html .= '<circle class="emlanlist-circle-svg" cx="11" cy="25" r="5"/>';
					$html .= '<circle class="emlanlist-circle-svg" cx="39" cy="25" r="5"/>';

					case 'fire':
					$html .= '<circle class="emlanlist-circle-svg" cx="11" cy="10" r="5"/>';
					$html .= '<circle class="emlanlist-circle-svg" cx="39" cy="40" r="5"/>';

					case 'to':
					$html .= '<circle class="emlanlist-circle-svg" cx="11" cy="40" r="5"/>';
					$html .= '<circle class="emlanlist-circle-svg" cx="39" cy="10" r="5"/>';
					break;

					case 'fem':
					$html .= '<circle class="emlanlist-circle-svg" cx="10" cy="10" r="5"/>';
					$html .= '<circle class="emlanlist-circle-svg" cx="40" cy="40" r="5"/>';

					case 'tre':
					$html .= '<circle class="emlanlist-circle-svg" cx="10" cy="40" r="5"/>';
					$html .= '<circle class="emlanlist-circle-svg" cx="40" cy="10" r="5"/>';

					case 'en':
					$html .= '<circle class="emlanlist-circle-svg" cx="25" cy="25" r="5"/>';
					break;

				}

				$html .= '</svg>';
			}

			// bestill 
			// $html .= '<div class="emlanlist-bestill-container">';

			$html .= EM_list_parts::button([
							// 'form' => true, 
							'name' => 'emlanlistse',
							'meta' => $meta,
							'button_text' => 'Ansök här!'
						]);

			// $html .= '</div>';



			$html .= '</div>'; // end-container

			$html .= '</form></li>';
		}

		$html .= '</ul>';

		return $html;
	}

	public function add_shortcode_landingside($atts = [], $content = null) {
		add_action('wp_footer', ['EM_list_parts', 'add_ga'], 0);
		// add_action('wp_footer', [$this, 'add_inline_script'], 0);
		add_action('wp_enqueue_scripts', [$this, 'add_css']);
		return EM_list_parts::landingside(['type' => 'emlanlistse', 'atts' => $atts, 'button_text' => 'Ansök Här!']);
	}

	/**
	 * wp filter for adding to internal serp
	 * array_push to $data
	 * $data['html'] to be printed
	 * 
	 * @param [Array] $data [filter]
	 */
	public function add_serp($data) {
		global $post;

		if ($post->post_type != $this->name) return $data;

		$exclude = get_option($this->name.'_exclude');
		if (!is_array($exclude)) $exclude = [];
		if (in_array($post->ID, $exclude)) return $data;

		$exclude_serp = get_option($this->name.'_exclude_serp');
		if (!is_array($exclude_serp)) $exclude_serp = [];
		if (in_array($post->ID, $exclude_serp)) return $data;

		$html['html'] = $this->get_html([$post]);

		array_push($data, $html);
		add_action('wp_enqueue_scripts', array($this, 'add_css'));

		return $data;
	}

	/**
	 * kisses the data
	 * recursive sanitizer
	 * 
	 * @param  Mixed $data Strings or Arrays
	 * @return Mixed       Kissed data
	 */
	private function esc_kses($data) {
		if (!is_array($data)) return wp_kses_post($data);

		$d = [];
		foreach($data as $key => $value)
			$d[$key] = $this->esc_kses($value);

		return $d;
	}
}