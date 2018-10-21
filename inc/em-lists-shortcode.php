<?php 



final class EML_sc {
	/* singleton */
	private static $instance = null;

	public static function get_instance() {
		if (self::$instance === null) self::$instance = new self();

		return self::$instance;
	}

	private function __construct() {
		
	}


	/* getting the posts */
	public static function posts($name, $sc, $atts, $content = null) {
		if (!is_array($atts)) $atts = [];

		$args = [
			'post_type' 		=> $name,
			'posts_per_page' 	=> -1,
			'orderby'			=> [
										'meta_value_num' => 'ASC',
										'title' => 'ASC'
								   ],
			'meta_key'			=> $name.'_sort'.($atts[$sc] ? '_'.sanitize_text_field($atts[$sc]) : '')
		];


		$type = false;
		if (isset($atts[$sc])) $type = $atts[$sc];
		if ($type)
			$args['tax_query'] = array(
					array(
						'taxonomy' => $name.'type',
						'field' => 'slug',
						'terms' => sanitize_text_field($type)
					)
				);


		$names = false;
		if (isset($atts['name'])) $names = explode(',', preg_replace('/ /', '', $atts['name']));
		if ($names) $args['post_name__in'] = $names;
		
		$exclude = get_option($name.'_exclude');

		if (is_array($exclude) && !empty($exclude)) $args['post__not_in'] = $exclude;

		$posts = get_posts($args);	

		$sorted_posts = [];
		if ($names) {
			foreach(explode(',', preg_replace('/ /', '', $atts['name'])) as $n)
				foreach($posts as $p) 
					if ($n === $p->post_name) array_push($sorted_posts, $p);
		
			$posts = $sorted_posts;
		}
				
		return $posts;
	}


	/**
	 * Show logo
	 * @param  String $name    name of post type
	 * @param  Array $atts    shortcode parametres
	 * @param  String $content shortcode content
	 * @return html          image html
	 */
	public function image($name, $atts, $content = null) {
		if (!isset($atts['name']) || $atts['name'] == '') return;

		$args = [
			'post_type' 		=> $name,
			'posts_per_page'	=> 1,
			'name' 				=> sanitize_text_field($atts['name'])
		];

		$post = get_posts($args);

		if (!is_array($post)) return;

		if (!get_the_post_thumbnail_url($post[0])) return;

		$meta = get_post_meta($post[0]->ID, $name.'_data');
		if (isset($meta[0])) $meta = $meta[0];
	

		$float = false;
		if ($atts['float']) 
			switch ($atts['float']) {
				case 'left': $float = ' style="float: left; margin-right: 3rem;"'; break;
				case 'right': $float = ' style="float: right; margin-left: 3rem;"'; break;
			}

		$html = '';

		if ($meta['bestill']) {

			if ($meta['qstring']) $meta['bestill'] = EM_list_tracking::query($meta['bestill'], $meta['ttemplate']);
			// adding tracking pixel
			// if ($meta['pixel']) {
			// 	if ($meta['qstring']) $html .= $this->add_pixel($this->add_query_string($meta['pixel'], $atts['source'], $atts['page']));
			// 	else $html .= $this->add_pixel($meta['pixel']);
			// }

			// fixing query string
			// if ($meta['qstring']) $meta['bestill'] = $this->add_query_string($meta['bestill'], $atts['source'], $atts['page']);

			// image with anchor
			return sprintf('<div class="%s-logo-ls"%s><a target="_blank" rel=noopner href="%s"><img class="%s-image" alt="%s" src="%s"></a></div>',
						$name,
						$float ? $float : '',
						esc_url($meta['bestill']),
						$name,
						esc_attr($post[0]->post_title),
						esc_url(get_the_post_thumbnail_url($post[0], 'full'))
					);
		}

		// no anchor
		return sprintf('<div class="%s-logo-ls"%s><img class="%s-image" alt="%s" src="%s"></div>',
						$name,
						$float ? $float : '',
						$name,
						esc_attr($post[0]->post_title),
						esc_url(get_the_post_thumbnail_url($post[0], 'full'))
					);
	}


	/**
	 * order link (button)
	 * @param  String $name    post type
	 * @param  Array $atts    shortcode parametre
	 * @param  String $content shortcode content
	 * @return html          button link html
	 */
	public function link($name, $atts, $content = null) {
		if (!isset($atts['name']) || $atts['name'] == '') return;

		$args = [
			'post_type' 		=> $name,
			'posts_per_page'	=> 1,
			'name' 				=> sanitize_text_field($atts['name'])
		];

		$post = get_posts($args);
		if (!is_array($post)) return;

		$meta = get_post_meta($post[0]->ID, $name.'_data');

		if (!is_array($meta)) return;

		$meta = $meta[0];

		if (!$meta['bestill']) return;

		
		$float = false;
		if ($atts['float']) 
			switch ($atts['float']) {
				case 'left': $float = ' style="float: left; margin-right: 3rem;"'; break;
				case 'right': $float = ' style="float: right; margin-left: 3rem;"'; break;
			}


		$html = '';

		// fixing query string
		// if ($meta['qstring']) $meta['bestill'] = $this->add_query_string($meta['bestill'], $atts['source'], $atts['page']);

		// adding tracking pixel
		// if ($meta['pixel']) {
		// 	if ($meta['qstring']) $html .= $this->add_pixel($this->add_query_string($meta['pixel'], $atts['source'], $atts['page']));
		// 	else $html .= $this->add_pixel($meta['pixel']);
		// }

		// $html .= '<div class="emlanlist-fatilbud-solo emlanlist-fatilbud-container-solo"'.($float ? $float : '').'><a target="_blank" rel="noopener" class="emlanlist-lenke-fatilbud emlanlist-lenke" href="'.esc_url($meta['bestill']).'"><svg class="emlanlist-svg" version="1.1" x="0px" y="0px" width="26px" height="20px" viewBox="0 0 26 20" enable-background="new 0 0 24 24" xml:space="preserve"><path fill="none" d="M0,0h24v24H0V0z"/><path class="emlanlist-thumb" d="M1,21h4V9H1V21z M23,10c0-1.1-0.9-2-2-2h-6.31l0.95-4.57l0.03-0.32c0-0.41-0.17-0.79-0.44-1.06L14.17,1L7.59,7.59C7.22,7.95,7,8.45,7,9v10c0,1.1,0.9,2,2,2h9c0.83,0,1.54-0.5,1.84-1.22l3.02-7.05C22.95,12.5,23,12.26,23,12V10z"/></svg> Søk her!</a></div>';
		// return $html;

		return sprintf('<div class="%s-order-solo %s-order-container"%s><a target="_blank" rel=noopener class="%s-order-link %s-link" href="%s">%s %s</a></div>',
						$name,
						$name,
						$float ? $float : '',
						$name,
						$name,
						esc_url($meta['bestill']),
						'<svg class="'.$name.'-svg" version="1.1" x="0px" y="0px" width="26px" height="20px" viewBox="0 0 26 20" enable-background="new 0 0 24 24" xml:space="preserve"><path fill="none" d="M0,0h24v24H0V0z"/><path class="emlanlist-thumb" d="M1,21h4V9H1V21z M23,10c0-1.1-0.9-2-2-2h-6.31l0.95-4.57l0.03-0.32c0-0.41-0.17-0.79-0.44-1.06L14.17,1L7.59,7.59C7.22,7.95,7,8.45,7,9v10c0,1.1,0.9,2,2,2h9c0.83,0,1.54-0.5,1.84-1.22l3.02-7.05C22.95,12.5,23,12.26,23,12V10z"/></svg>',
						'Søk Nå'
					);
	}

}