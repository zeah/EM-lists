<?php 

/**
 * WP Shortcodes
 */
final class Lan_shortcode {
	/* singleton */
	private static $instance = null;

	// private $pixel = false;

	public $pixels = [];

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
		else add_shortcode('emlanlist', array($this, 'add_shortcode'));

		// loan thumbnail
		if (!shortcode_exists('lan-bilde')) add_shortcode('lan-bilde', array($this, 'add_shortcode_bilde'));
		else add_shortcode('emlanlist-bilde', array($this, 'add_shortcode_bilde'));

		// loan button
		if (!shortcode_exists('lan-bestill')) add_shortcode('lan-bestill', array($this, 'add_shortcode_bestill'));
		else add_shortcode('emlanlist-bestill', array($this, 'add_shortcode_bestill'));


		add_filter('search_first', array($this, 'add_serp'));
		// add_action('wp_footer', array($this, 'add_pixel_footer'));



	}



	/**
	 * returns a list of loans
	 */
	public function add_shortcode($atts, $content = null) {

		add_action('wp_enqueue_scripts', array($this, 'add_css'));

		return $this->get_html(EML_sc::posts('emlanlist', 'lan', $atts, $content), $atts);

	}



	/**
	 * returns only thumbnail from loan
	 */
	public function add_shortcode_bilde($atts, $content = null) {
		if (!isset($atts['name']) || $atts['name'] == '') return;

		add_action('wp_enqueue_scripts', array($this, 'add_css'));

		return EML_sc::image('emlanlist', $atts, $content);
	}



	/**
	 * returns bestill button only from loan
	 */
	public function add_shortcode_bestill($atts, $content = null) {
		if (!isset($atts['name']) || $atts['name'] == '') return;

		add_action('wp_enqueue_scripts', array($this, 'add_css'));

		return EML_sc::link('emlanlist', $atts, $content);
	}



	/**
	 * adding sands to head
	 */
	public function add_css() {
        wp_enqueue_style('emlanlist-style', LANLIST_PLUGIN_URL.'assets/css/pub/em-lanlist.css', array(), '1.0.2', '(min-width: 951px)');
        wp_enqueue_style('emlanlist-mobile', LANLIST_PLUGIN_URL.'assets/css/pub/em-lanlist-mobile.css', array(), '1.0.2', '(max-width: 950px)');
	}



	/**
	 * returns the html of a list of loans
	 * @param  WP_Post $posts a wp post object
	 * @return [html]        html list of loans
	 */
	private function get_html($posts, $atts) {
		if (!$atts) $atts = [];
		$html = '<ul class="emlanlist-ul">';

		$star = '<svg class="emlanlist-star" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"><path d="M0 0h24v24H0z" fill="none"/><path class="emlanlist-star-path" d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/><path d="M0 0h24v24H0z" fill="none"/></svg>';

		foreach ($posts as $p) {
			
			$meta = get_post_meta($p->ID, 'emlanlist_data');
			// skip if no meta found
			if (isset($meta[0])) $meta = $meta[0];
			else continue;

			// sanitize meta
			$html .= '<li class="emlanlist-container">';
			
			// if ($meta['qstring']) $meta['bestill'] = $this->add_query_string($meta['bestill'], $atts['source'], $atts['page']);

			// if ($meta['pixel']) {
			// 	if ($meta['qstring']) $html .= $this->add_pixel($this->add_query_string($meta['pixel'], $atts['source'], $atts['page']));
			// 	else $html .= $this->add_pixel($meta['pixel']);
			// }

			$meta = $this->esc_kses($meta);

			$html .= '<div class="emlanlist-row emlanlist-toprow">';

			$html .= '<div class="emlanlist-logo"><a target="_blank" rel="noopener" href="'.$meta['readmore'].'"><img class="emlanlist-image" src="'.wp_kses_post(get_the_post_thumbnail_url($p,'post-thumbnail')).'"></a></div>';

			if ($meta['info05']) $html .= '<div class="emlanlist-belop emlanlist-top-info">'.$meta['info05'].'</div>';

			if ($meta['info06']) $html .= '<div class="emlanlist-nedbetaling emlanlist-top-info">'.$meta['info06'].'</div>';

			if ($meta['info07']) $html .= '<div class="emlanlist-alder emlanlist-top-info">'.$meta['info07'].'</div>';

			if ($meta['info04']) $html .= '<div class="emlanlist-effrente emlanlist-top-info">'.$meta['info04'].'</div>';

			if ($meta['bestill']) $html .= '<div class="emlanlist-order"><a class="emlanlist-link emlanlist-order-link" target="_blank" rel=noopener href="'.esc_url($meta['bestill']).'">Få Tilbud Nå</a></div>';
			
			$html .= '</div>';


			$html .= '<div class="emlanlist-row emlanlist-middlerow">';

			// info 1
			if ($meta['info01']) $html .= '<div class="emlanlist-info emlanlist-info-en">'.$star.'<span>'.$meta['info01'].'</span></div>';

			// info 2
			if ($meta['info02']) $html .= '<div class="emlanlist-info emlanlist-info-to">'.$star.'<span>'.$meta['info02'].'</span></div>';

			// info 3
			if ($meta['info03']) $html .= '<div class="emlanlist-info emlanlist-info-tre">'.$star.'<span>'.$meta['info03'].'</span></div>';

			$html .= '</div>';

			$html .= '<div class="emlanlist-row emlanlist-bottomrow">';

			if ($meta['info08']) $html .= '<div class="emlanlist-info emlanlist-info-atte">'.$meta['info08'].'</div>';

			if ($meta['readmore']) $html .= '<div class="emlanlist-lesmer"><a class="emlanlist-lenke-lesmer" href="'.esc_url($meta['readmore']).'">les mer</a></div>';


			$html .= '</div>';

			$html .= '</li>';
		}

		$html .= '</ul>';
		return $html;
	}















	// private function add_pixel($pixel) {
	// 	if ($this->pixels[$pixel]) return '';

	// 	$this->pixels[$pixel] = true;

	// 	return '<img width=0 height=0 src="'.esc_url($pixel).'" style="position:absolute">';
	// }













	private function add_query_string($link = null, $source = null, $page = null) {
		
		$axo = [
			'page' => 'aff_sub2',
			'source' => 'source',
			'click_id' => 'aff_click_id',
			'type' => 'aff_sub'
		];

		$adservice = [
			'page' => 'sub'
		];

		if (strpos($link, 'axo') !== false) $def = $axo;
		elseif (strpos($link, 'adservice') !== false) $def = $adservice;
		else $def = $axo; // axo is currently default

		// parsing given url for query string
		parse_str(parse_url($link)['query'], $url_query);

		// parsing current url for query string
		parse_str($_SERVER['QUERY_STRING'], $result);
		// wp_die('<xmp>'.print_r($url_query, true).'</xmp>');
		
		// source
		if ($def['source']) {
			if ($source) $result[$def['source']] = $source;
			// elseif ($url_query[$def['source']]) $result[$def['source']] = $url_query[$def['source']];
			elseif (!$url_query[$def['source']]) $result[$def['source']] = $_SERVER['SERVER_NAME'];
		}


		// page (aff_sub2)
		if ($def['page']) {
			global $post;
			// global $pagename;
			if ($page) $result[$def['page']] = $page;
			// elseif ($url_query[$def['page']]) $result[$def['page']] = $url_query[$def['page']]; 
			elseif (!$url_query[$def['page']]) $result[$def['page']] = $post->post_name;
		}
			// wp_die('<xmp>'.print_r($result, true).'</xmp>');
		// aff_sub 
		// aff_click_id
		// from google ad, bing ad or organic
		if ($def['type']) {
		$result[$def['type']] = 'organic';
		foreach($result as $key => $value)
			switch ($key) {
				case 'gclid': 
					$result[$def['type']] = 'google'; 
					$result[$def['click_id']] = $value;
					break;
				case 'msclkid': 
					$result[$def['type']] = 'bing'; 
					$result[$def['click_id']] = $value;
					break;
			}
		// removing google ad and bing ad parameter
			unset($result['gclid']);
			unset($result['msclkid']);
		}

		return add_query_arg($result, $link);
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

		if ($post->post_type != 'emlanlist') return $data;

		$exclude = get_option('emlanlist_exclude');
		if (!is_array($exclude)) $exclude = [];
		if (in_array($post->ID, $exclude)) return $data;

		$exclude_serp = get_option('emlanlist_exclude_serp');
		if (!is_array($exclude_serp)) $exclude_serp = [];
		if (in_array($post->ID, $exclude_serp)) return $data;

		$html['html'] = $this->get_html([$post]);

		array_push($data, $html);
		add_action('wp_enqueue_scripts', array($this, 'add_css'));

		return $data;
	}















	// private function add_source($meta, $source) {

	// 	// removing current source
	// 	if (preg_match('/(?:(?!\?|&))(?:source=.*?)(?:(&|$))/', $meta, $matches))
	// 		$meta = str_replace($matches[0], '', $meta); 
	// 	$meta = preg_replace('/(\?|&)$/', '', $meta);

	// 	// adding source
	// 	if (strpos($meta, '?') !== false) $meta .= '&source=' . $source;
	// 	else $meta .= '?source=' . $source;

	// 	return $meta;
	// }


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