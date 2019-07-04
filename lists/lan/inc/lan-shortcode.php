<?php 

/**
 * WP Shortcodes
 */
final class Lan_shortcode {
	/* singleton */
	private static $instance = null;


	public static function get_instance() {
		if (self::$instance === null) self::$instance = new self();

		return self::$instance;
	}

	private function __construct() {

		$this->wp_hooks();
	}


	/**
	 * hooks for wp
	 */
	private function wp_hooks() {

		// loan list
		if (!shortcode_exists('lan')) add_shortcode('lan', array($this, 'add_shortcode'));
		else add_shortcode(EMLAN, array($this, 'add_shortcode'));

		// loan thumbnail
		if (!shortcode_exists('lan-bilde')) add_shortcode('lan-bilde', array($this, 'add_shortcode_bilde'));
		else add_shortcode(EMLAN.'-bilde', array($this, 'add_shortcode_bilde'));

		// loan button
		if (!shortcode_exists('lan-bestill')) add_shortcode('lan-bestill', array($this, 'add_shortcode_bestill'));
		else add_shortcode(EMLAN.'-bestill', array($this, 'add_shortcode_bestill'));

		// button and clickable logo
		if (!shortcode_exists('lan-landingside')) add_shortcode('lan-landingside', array($this, 'add_shortcode_landingside'));
		else add_shortcode(EMLAN_SE.'-landingside', array($this, 'add_shortcode_landingside'));


		add_filter('search_first', array($this, 'add_serp'));

	}



	/**
	 * returns a list of loans
	 */
	public function add_shortcode($atts, $content = null) {
		add_action('wp_enqueue_scripts', [$this, 'add_css']);
		add_action('wp_footer', ['EM_list_parts', 'add_ga'], 0);

		return $this->get_html(EM_list_sc::posts(EMLAN, 'lan', $atts, $content), $atts);
	}



	/**
	 * returns only thumbnail from loan
	 */
	public function add_shortcode_bilde($atts, $content = null) {
		if (!isset($atts['name']) || $atts['name'] == '') return;

		add_action('wp_enqueue_scripts', [$this, 'add_css']);

		return EM_list_parts::logo([
				'image' => wp_kses_post(get_the_post_thumbnail_url(EM_list_parts::gp($atts['name'], EMLAN),'post-thumbnail')),
				'title' => 'Søk Nå',
				'name' => EMLAN,
				'atts' => $atts
			]);
	}



	/**
	 * returns bestill button only from loan
	 */
	public function add_shortcode_bestill($atts, $content = null) {
		return EM_list_parts::sc_button($atts, EMLAN, 'Søk Nå', $this, 'add_css');
	}

	public function add_shortcode_landingside($atts = [], $content = null) {
		add_action('wp_footer', ['EM_list_parts', 'add_ga'], 0);
		add_action('wp_enqueue_scripts', [$this, 'add_css']);
		return EM_list_parts::landingside(['type' => EMLAN, 'atts' => $atts, 'button_text' => 'Søk Nå']);
	}

	/**
	 * adding sands to head
	 */
	public function add_css() {
        wp_enqueue_style(EMLAN.'-style', LANLIST_PLUGIN_URL.'assets/css/pub/em-lanlist.css', array(), '1.0.3', '(min-width: 951px)');
        wp_enqueue_style(EMLAN.'-mobile', LANLIST_PLUGIN_URL.'assets/css/pub/em-lanlist-mobile.css', array(), '1.0.3', '(max-width: 950px)');
	}



	/**
	 * returns the html of a list of loans
	 * @param  WP_Post $posts a wp post object
	 * @return [html]        html list of loans
	 */
	private function get_html($posts, $atts = null) {
		// wp_die('<xmp>'.print_r($posts, true).'</xmp>');
		if (!$atts) $atts = [];
		$html = sprintf('<ul class="%s-ul">', EMLAN);

		$star = sprintf(
			'<svg class="%1$s-star" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"><path d="M0 0h24v24H0z" fill="none"/><path class="%1$s-star-path" d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/><path d="M0 0h24v24H0z" fill="none"/></svg>',
			EMLAN
		);



		foreach ($posts as $p) {
			$meta = get_post_meta($p->ID, EMLAN.'_data');

			// skip if no meta found
			if (isset($meta[0])) $meta = $meta[0];
			else continue;

			$html .= sprintf(
				'<li class="%1$s-list"><form class="%1$s-container" target="_blank" rel=nofollow action="%2$s" method="get">',
				EMLAN, 
				preg_replace('/\?.*$/', '', $meta['bestill'])
			);

			$meta = $this->esc_kses($meta);

			$logo = EM_list_parts::logo([
				'image' => wp_kses_post(get_the_post_thumbnail_url($p,'post-thumbnail')),
				'meta' => $meta,
				'title' => 'Søk Nå',
				'name' => EMLAN

			]);

			// $top
			$top = '';
			foreach ([['belop','info05'], ['nedbetaling', 'info06'], ['alder', 'info07'], ['effrente', 'info04']] as $ti)
				if (isset($meta[$ti[1]]) && $meta[$ti[1]])
					$top .= sprintf(
						'<div class="%1$s-%2$s %1$s-top-info">%3$s</div>',

						EMLAN,

						$ti[0],

						$meta[$ti[1]]
					);

			$order = sprintf(
				'<div class="%s-order">%s</div>',

				EMLAN,

				EM_list_parts::button([
							'name' => EMLAN,
							'meta' => $meta,
							'button_text' => 'Søk Nå'
						])
			);


			// top row
			$html .= sprintf(
				'<div class="%1$s-row %1$s-toprow">%2$s%3$s%4$s</div>', 
				EMLAN,
				$logo,
				$top,
				$order
			);


			$middle = '';
			foreach ([['en','info01'], ['to', 'info02'], ['tre', 'info03']] as $val)
				if (isset($meta[$val[1]]) && $meta[$val[1]])
					$middle .= sprintf(
						'<div class="%1$s-info %1$s-info-%2$s">%3$s %4$s</div>',

						EMLAN,

						$val[0],

						$star,

						$meta[$val[1]]
					);

			// middle row
			$html .= sprintf(
				'<div class="%1$s-row %1$s-middlerow">%2$s</div>',
				EMLAN,
				$middle
			);


			// bottom
			$html .= sprintf(
				'<div class="%1$s-row %1$s-bottomrow">%2$s%3$s</div>',

				EMLAN,

				(isset($meta['info08']) && $meta['info08']) 
					? sprintf(
						'<div class="%1$s-info %1$s-info-atte">%2$s</div>', 
						EMLAN, 
						$meta['info08']) 
					: '',
				
				(isset($meta['readmore']) && $meta['readmore']) 
					? sprintf(
						'<div class="%1$s-lesmer"><a class="%1$s-lenke-lesmer" href="%2$s"></a></div>', 
						EMLAN, 
						esc_url($meta['readmore'])) 
					: ''
			);


			$html .= '</form></li>';

			
		}

		$html .= '</ul>';
		return $html;
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

		if ($post->post_type != EMLAN) return $data;

		$exclude = get_option(EMLAN.'_exclude');
		if (!is_array($exclude)) $exclude = [];
		if (in_array($post->ID, $exclude)) return $data;

		$exclude_serp = get_option(EMLAN.'_exclude_serp');
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