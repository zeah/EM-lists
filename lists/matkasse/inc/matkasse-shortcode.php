<?php 

/**
 * WP Shortcodes
 */
final class Matkasse_shortcode {
	/* singleton */
	private static $instance = null;

	// private $name = 'matkasselist';

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
		if (!shortcode_exists('mat')) add_shortcode('mat', [$this, 'add_shortcode']);
		else add_shortcode(MAT, [$this, 'add_shortcode']);

		// // loan thumbnail
		// if (!shortcode_exists('mat-bilde')) add_shortcode('mat-bilde', [$this, 'add_shortcode_bilde']);
		// else add_shortcode(MAT.'-bilde', [$this, 'add_shortcode_bilde']);

		// // loan button
		// if (!shortcode_exists('mat-bestill')) add_shortcode('mat-bestill', [$this, 'add_shortcode_bestill']);
		// else add_shortcode(MAT.'-bestill', [$this, 'add_shortcode_bestill']);


		add_filter('search_first', [$this, 'add_serp']);

	}



	/**
	 * returns a list of loans
	 */
	public function add_shortcode($atts, $content = null) {

		add_action('wp_enqueue_scripts', array($this, 'add_css'));

		return $this->get_html(EM_list_sc::posts(MAT, 'mat', $atts, $content), $atts);

	}



	/**
	 * returns only thumbnail from loan
	 */
	public function add_shortcode_bilde($atts, $content = null) {
		if (!isset($atts['name']) || $atts['name'] == '') return;

		add_action('wp_enqueue_scripts', array($this, 'add_css'));

		return EM_list_sc::image('matkasselist', $atts, $content);
	}



	/**
	 * returns bestill button only from loan
	 */
	public function add_shortcode_bestill($atts, $content = null) {
		if (!isset($atts['name']) || $atts['name'] == '') return;

		add_action('wp_enqueue_scripts', array($this, 'add_css'));

		return EM_list_sc::link('matkasselist', $atts, $content);
	}



	/**
	 * adding sands to head
	 */
	public function add_css() {
        wp_enqueue_style('matkasselist-style', MATKASSELIST_PLUGIN_URL.'assets/css/pub/em-matkasselist.css', array(), '1.0.0', '(min-width: 951px)');
        wp_enqueue_style('matkasselist-mobile', MATKASSELIST_PLUGIN_URL.'assets/css/pub/em-matkasselist-mobile.css', array(), '1.0.0', '(max-width: 950px)');
	}

	private function get_html($posts, $atts = null) {
		// wp_die('<xmp>'.print_r($posts, true).'</xmp>');
		// if (!$atts) $atts = [];
		$html = sprintf('<ul class="%s-ul">', MAT);
		// wp_die('<xmp>'.print_r($posts, true).'</xmp>');
		$star = sprintf(
			'<svg class="%1$s-star" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"><path d="M0 0h24v24H0z" fill="none"/><path class="%1$s-star-path" d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/><path d="M0 0h24v24H0z" fill="none"/></svg>',
			MAT
		);



		foreach ($posts as $p) {
			$meta = get_post_meta($p->ID, MAT.'_data');

			// skip if no meta found
			if (isset($meta[0])) $meta = $meta[0];
			else continue;

			$html .= sprintf(
				'<li class="%1$s-list"><form class="%1$s-container" target="_blank" rel=nofollow action="%2$s" method="get">',
				MAT, 
				preg_replace('/\?.*$/', '', $meta['bestill'])
			);

			$meta = $this->esc_kses($meta);

			$logo = EM_list_parts::logo([
				'image' => wp_kses_post(get_the_post_thumbnail_url($p,'post-thumbnail')),
				'meta' => $meta,
				'title' => 'Bestill nå',
				'name' => MAT

			]);

			// $top
			$top = '';
			foreach ([['navn','info05'], ['dager', 'info06'], ['personer', 'info07'], ['pris', 'info04']] as $ti)
				if (isset($meta[$ti[1]]) && $meta[$ti[1]])
					$top .= sprintf(
						'<div class="%1$s-%2$s %1$s-top-info">%3$s</div>',

						MAT,

						$ti[0],

						$meta[$ti[1]]
					);

			$order = sprintf(
				'<div class="%s-order">%s</div>',

				MAT,

				EM_list_parts::button([
							'name' => MAT,
							'meta' => $meta,
							'button_text' => 'Bestill'
						])
			);


			// top row
			$html .= sprintf(
				'<div class="%1$s-row %1$s-toprow">%2$s%3$s%4$s</div>', 
				MAT,

				EM_list_parts::logo([
					'image' => wp_kses_post(get_the_post_thumbnail_url($p,'post-thumbnail')),
					'meta' => $meta,
					'title' => 'Søk Nå',
					'name' => MAT
				]),

				$top,

				$order
			);


			$middle = '';
			foreach ([['en','info01'], ['to', 'info02'], ['tre', 'info03']] as $val)
				if (isset($meta[$val[1]]) && $meta[$val[1]])
					$middle .= sprintf(
						'<div class="%1$s-info %1$s-info-%2$s">%3$s %4$s</div>',

						MAT,

						$val[0],

						$star,

						$meta[$val[1]]
					);

			// middle row
			$html .= sprintf(
				'<div class="%1$s-row %1$s-middlerow">%2$s</div>',
				MAT,
				$middle
			);


			// bottom
			$html .= sprintf(
				'<div class="%1$s-row %1$s-bottomrow">%2$s%3$s</div>',

				MAT,

				(isset($meta['info08']) && $meta['info08']) 
					? sprintf(
						'<div class="%1$s-info %1$s-info-atte">%2$s</div>', 
						MAT, 
						$meta['info08']) 
					: '',
				
				(isset($meta['readmore']) && $meta['readmore']) 
					? sprintf(
						'<div class="%1$s-lesmer"><a class="%1$s-lenke-lesmer" href="%2$s"></a></div>', 
						MAT, 
						esc_url($meta['readmore'])) 
					: ''
			);


			$html .= '</form></li>';

			
		}

		$html .= '</ul>';
		return $html;
	}

	/**
	 * returns the html of a list of loans
	 * @param  WP_Post $posts a wp post object
	 * @return [html]        html list of loans
	 */
	// private function get_html2($posts, $atts = null) {
	// 	if (!$atts) $atts = [];
	// 	$html = sprintf('<ul class="%s-ul">', MAT);

	// 	$star = sprintf('<svg class="%1$s-star" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"><path d="M0 0h24v24H0z" fill="none"/><path class="%1$s-star-path" d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/><path d="M0 0h24v24H0z" fill="none"/></svg>', MAT);

	// 	foreach ($posts as $p) {
			
	// 		$meta = get_post_meta($p->ID, MAT.'_data');

	// 		// skip if no meta found
	// 		if (isset($meta[0])) $meta = $meta[0];
	// 		else continue;

	// 		// $redir = get_post_meta($p->ID, $this->name.'_redirect');
	// 		// if (isset($redir[0]) && $redir[0]) $redir = true;
	// 		// else $redir = false;
			
	// 		// sanitize meta
	// 		$html .= sprintf(
	// 			'<li class="%1$s-list">
	// 				<form target="_blank" rel=nofollow class="%1$s-container" action="%2$s" method="get">
	// 				</form>

	// 				<div class="%1$s-row %1$s-toprow">
	// 				%3$s
	// 				</div>

	// 			</li>',

	// 			MAT,

	// 			preg_replace('/\?.*$/', '', $meta['bestill'])



	// 		);
			
	// 		// if ($redir) $meta['bestill'] = EM_list_sc::add_site($p->post_name.$this->pf);

	// 		// if (isset($meta['qstring']) && $meta['qstring']) { 
	// 			// if ($meta['pixel']) $html .= EM_list_tracking::pixel($meta['pixel'], $meta['ttemplate']);
	// 			// $meta['bestill'] = EM_list_tracking::query($meta['bestill'], $meta['ttemplate']);
	// 		// }

	// 		$meta = $this->esc_kses($meta);

	// 		$html .= '<div class="matkasselist-row matkasselist-toprow">';

	// 		$html .= '<div class="matkasselist-logo"><a target="_blank" rel="noopener" href="'.$meta['readmore'].'"><img class="matkasselist-image" src="'.wp_kses_post(get_the_post_thumbnail_url($p,'post-thumbnail')).'"></a></div>';


	// 		if ($meta['info05']) $html .= '<div class="matkasselist-navn matkasselist-top-info">'.$meta['info05'].'</div>';

	// 		if ($meta['info06']) $html .= '<div class="matkasselist-dager matkasselist-top-info">'.$meta['info06'].'</div>';

	// 		if ($meta['info07']) $html .= '<div class="matkasselist-personer matkasselist-top-info">'.$meta['info07'].'</div>';

	// 		if ($meta['info04']) $html .= '<div class="matkasselist-pris matkasselist-top-info">'.$meta['info04'].'</div>';

	// 		if ($meta['bestill']) $html .= '<div class="matkasselist-order"><a class="matkasselist-link matkasselist-order-link" target="_blank" rel=noopener href="'.esc_url($meta['bestill']).'"></a></div>';
			
	// 		$html .= '</div>';


	// 		$html .= '<div class="matkasselist-row matkasselist-middlerow">';

	// 		// info 1
	// 		if ($meta['info01']) $html .= '<div class="matkasselist-info matkasselist-info-en">'.$star.'<span>'.$meta['info01'].'</span></div>';

	// 		// info 2
	// 		if ($meta['info02']) $html .= '<div class="matkasselist-info matkasselist-info-to">'.$star.'<span>'.$meta['info02'].'</span></div>';

	// 		// info 3
	// 		if ($meta['info03']) $html .= '<div class="matkasselist-info matkasselist-info-tre">'.$star.'<span>'.$meta['info03'].'</span></div>';

	// 		$html .= '</div>';

	// 		$html .= '<div class="matkasselist-row matkasselist-bottomrow">';

	// 		if ($meta['info08']) $html .= '<div class="matkasselist-info matkasselist-info-atte">'.$meta['info08'].'</div>';

	// 		if ($meta['readmore']) $html .= '<div class="matkasselist-lesmer"><a class="matkasselist-lenke-lesmer" href="'.esc_url($meta['readmore']).'"></a></div>';


	// 		$html .= '</div>';

	// 		$html .= '</li>';

			
	// 	}

	// 	$html .= '</ul>';
	// 	return $html;
	// }



	/**
	 * wp filter for adding to internal serp
	 * array_push to $data
	 * $data['html'] to be printed
	 * 
	 * @param [Array] $data [filter]
	 */
	public function add_serp($data) {
		global $post;

		if ($post->post_type != 'matkasselist') return $data;

		$exclude = get_option('matkasselist_exclude');
		if (!is_array($exclude)) $exclude = [];
		if (in_array($post->ID, $exclude)) return $data;

		$exclude_serp = get_option('matkasselist_exclude_serp');
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