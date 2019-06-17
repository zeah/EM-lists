<?php 

/**
 * WP Shortcodes
 */
final class Lan_shortcode {
	/* singleton */
	private static $instance = null;

	// private $name = 'emlanlist';

	// private $pf = '-get';

	public static function get_instance() {
		if (self::$instance === null) self::$instance = new self();

		return self::$instance;
	}

	private function __construct() {

		// $pf = get_option('em_lists');
		// if (isset($pf['redir_pf']) && $pf['redir_pf']) $this->pf = '-'.ltrim($pf['redir_pf'], '-');


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


		add_filter('search_first', array($this, 'add_serp'));

	}



	/**
	 * returns a list of loans
	 */
	public function add_shortcode($atts, $content = null) {

		// wp_die('<xmp>'.print_r($_SERVER, true).'</xmp>');

		// $temp = '/dill/test';

		// preg_match('/(?:.*)\/(.*)-.*$/', $temp, $matches);

		// if (!$matches) preg_match('/(?:.*\/)(.*)/', $temp, $matches);



		// wp_die('<xmp>'.print_r($matches, true).'</xmp>');
		


		// $dl = $_SERVER['REDIRECT_URL'];
		// $ip = $_SERVER['REMOTE_ADDR'];
		// $t = 'event';
		// $ec = 'List Plugin Clicks';
		// $ea = 'clicks';
		// $el = $matches[1];
		
		// // referrer; page; ip; domain; 
		// $content = wp_remote_post('https://www.google-analytics.com/debug/collect', array(
		// 	'method' => 'POST',
		// 	'timeout' => 30,
		// 	'redirection' => 5,
		// 	'httpversion' => '1.0',
		// 	// 'blocking' => false,
		// 	'headers' => array(),
		// 	'body' => [
		// 		'v' => '1', 
		// 		'tid' => 'UA-123456-1', 
		// 		'cid' => '555', 
		// 		'uip' => $ip, 
		// 		't' => $t, 
		// 		'ec' => $ec, 
		// 		'ea' => $ea, 
		// 		'el' => $el, 
		// 		'dl' => $dl
		// 	],
		// 	'cookies' => array()
		// 	)
		// );

		// wp_die('<xmp>'.print_r($content['body'], true).'</xmp>');
		
		// return 'hi';

		add_action('wp_enqueue_scripts', array($this, 'add_css'));

		return $this->get_html(EM_list_sc::posts(EMLAN, 'lan', $atts, $content), $atts);

	}



	/**
	 * returns only thumbnail from loan
	 */
	public function add_shortcode_bilde($atts, $content = null) {
		// if (!isset($atts['name']) || $atts['name'] == '') return;

		// add_action('wp_enqueue_scripts', array($this, 'add_css'));

		// return EM_list_sc::image('emlanlist', $atts, $content);

		if (!isset($atts['name']) || $atts['name'] == '') return;

		add_action('wp_enqueue_scripts', [$this, 'add_css']);
		add_action('wp_footer', ['EM_list_parts', 'add_ga'], 0);

		return EM_list_parts::logo([
				'image' => wp_kses_post(get_the_post_thumbnail_url(EM_list_parts::gp($atts['name'], EMLAN),'post-thumbnail')),
				'title' => 'Søk Nå',
				'name' => EMLAN
			]);
	}



	/**
	 * returns bestill button only from loan
	 */
	public function add_shortcode_bestill($atts, $content = null) {
		return EM_list_parts::sc_button($atts, EMLAN, 'Søk Nå', $this, 'add_css');
		// if (!isset($atts['name']) || $atts['name'] == '') return;

		// add_action('wp_enqueue_scripts', array($this, 'add_css'));

		// return EM_list_sc::link('emlanlist', $atts, $content);
	}



	/**
	 * adding sands to head
	 */
	public function add_css() {
        wp_enqueue_style('emlanlist-style', LANLIST_PLUGIN_URL.'assets/css/pub/em-lanlist.css', array(), '1.0.3', '(min-width: 951px)');
        wp_enqueue_style('emlanlist-mobile', LANLIST_PLUGIN_URL.'assets/css/pub/em-lanlist-mobile.css', array(), '1.0.3', '(max-width: 950px)');
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

			// $redir = get_post_meta($p->ID, EMLAN.'_redirect');
			// if (isset($redir[0]) && $redir[0]) $redir = true;
			// else $redir = false;
			
			// sanitize meta
			// $html .= '<li class="emlanlist-container">';
			$html .= sprintf(
				'<li class="%1$s-list"><form class="%1$s-container" target="_blank" rel=nofollow action="%2$s" method="get">',
				EMLAN, 
				preg_replace('/\?.*$/', '', $meta['bestill'])
			);
			// if ($redir) $meta['bestill'] = EM_list_sc::add_site($p->post_name.$this->pf);

			// if (isset($meta['qstring']) && $meta['qstring']) { 
			// 	if ($meta['pixel']) $html .= EM_list_tracking::pixel($meta['pixel'], $meta['ttemplate']);
			// 	$meta['bestill'] = EM_list_tracking::query($meta['bestill'], $meta['ttemplate']);
			// }

			$meta = $this->esc_kses($meta);

			$logo = EM_list_parts::logo([
				'image' => wp_kses_post(get_the_post_thumbnail_url($p,'post-thumbnail')),
				'meta' => $meta,
				'title' => 'Søk Nå',
				'name' => EMLAN

			]);

			$html .= sprintf(
				'<div class="%1$s-row %1$s-toprow">%2$s', 
				EMLAN,
				$logo
			);

			// $html .= '<div class="emlanlist-logo"><a target="_blank" rel="noopener" href="'.$meta['readmore'].'"><img class="emlanlist-image" src="'.wp_kses_post(get_the_post_thumbnail_url($p,'post-thumbnail')).'"></a></div>';

			$top_info = [['belop','info05'], ['nedbetaling', 'info06'], ['alder', 'info07'], ['effrente', 'info04']];

			$top
			foreach ($top_info as $ti)
				if (isset($meta[$ti[1]]) && $meta[$ti[1]])
					$html .= sprintf(
						'<div class="%1$s-%2$s %1$s-top-info">%3$s</div>',

						EMLAN,

						$ti[0],

						$meta[$ti[1]]
					);		

			// if ($meta['info05']) $html .= '<div class="emlanlist-belop emlanlist-top-info">'.$meta['info05'].'</div>';

			// if ($meta['info06']) $html .= '<div class="emlanlist-nedbetaling emlanlist-top-info">'.$meta['info06'].'</div>';

			// if ($meta['info07']) $html .= '<div class="emlanlist-alder emlanlist-top-info">'.$meta['info07'].'</div>';

			// if ($meta['info04']) $html .= '<div class="emlanlist-effrente emlanlist-top-info">'.$meta['info04'].'</div>';

			// if ($meta['bestill']) $html .= '<div class="emlanlist-order"><a class="emlanlist-link emlanlist-order-link" target="_blank" rel=noopener href="'.esc_url($meta['bestill']).'"></a></div>';
			
			$html .= sprintf(
				'<div class="%s-order">%s</div>',

				EMLAN,

				EM_list_parts::button([
							'name' => EMLAN,
							'meta' => $meta,
							'button_text' => 'Søk Nå'
						])
			);

			$html .= '</div>';


			$html .= sprintf('<div class="%1$s-row %1$s-middlerow">', EMLAN);

			// info 1
			if ($meta['info01']) $html .= '<div class="emlanlist-info emlanlist-info-en">'.$star.'<span>'.$meta['info01'].'</span></div>';

			// info 2
			if ($meta['info02']) $html .= '<div class="emlanlist-info emlanlist-info-to">'.$star.'<span>'.$meta['info02'].'</span></div>';

			// info 3
			if ($meta['info03']) $html .= '<div class="emlanlist-info emlanlist-info-tre">'.$star.'<span>'.$meta['info03'].'</span></div>';

			$html .= '</div>';

			$html .= '<div class="emlanlist-row emlanlist-bottomrow">';

			if ($meta['info08']) $html .= '<div class="emlanlist-info emlanlist-info-atte">'.$meta['info08'].'</div>';

			if ($meta['readmore']) $html .= '<div class="emlanlist-lesmer"><a class="emlanlist-lenke-lesmer" href="'.esc_url($meta['readmore']).'"></a></div>';


			$html .= '</div>';

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