<?php 

defined('ABSPATH') or die('Blank Space');


final class EM_list_sc {
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

		// wp_die('<xmp>'.print_r($name, true).'</xmp>');

		if (!is_array($atts)) $atts = [];
		$type = false;
		if (isset($atts[$sc])) $type = $atts[$sc];
		if (isset($atts['type'])) $type = $atts['type'];

		$args = [
			'post_type' 		=> $name,
			'posts_per_page' 	=> -1,
			'orderby'			=> [
										'meta_value_num' => 'ASC',
										'title' => 'ASC'
								   ],
			'meta_key'			=> $name.'_sort'.($type ? '_'.sanitize_text_field($type) : '')
		];


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
		// wp_die('<xmp>'.print_r($args, true).'</xmp>');
		$posts = get_posts($args);	

		// wp_die('<xmp>'.print_r($posts, true).'</xmp>');

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
		// global $post;
		// wp_die('<xmp>'.print_r(get_permalink($post), true).'</xmp>');
		
		if (!isset($atts['name']) || $atts['name'] == '') return;

		$args = [
			'post_type' 		=> $name,
			'posts_per_page'	=> 1,
			'name' 				=> sanitize_text_field($atts['name'])
		];

		$post = get_posts($args);
		// wp_die('<xmp>'.print_r($post, true).'</xmp>');
		
		if (!is_array($post)) return;
		$p = $post[0];

		if (!get_the_post_thumbnail_url($post[0])) return;

		$meta = get_post_meta($post[0]->ID, $name.'_data');
		if (isset($meta[0])) $meta = $meta[0];
		
		$redir = get_post_meta($post[0]->ID, $name.'_redirect');
		if (isset($redir[0])) $redir = $redir[0];

		$float = false;
		if ($atts['float']) 
			switch ($atts['float']) {
				case 'left': $float = ' style="float: left; margin-right: 3rem;"'; break;
				case 'right': $float = ' style="float: right; margin-left: 3rem;"'; break;
			}

		$html = '';
		// wp_die('<xmp>'.print_r($meta, true).'</xmp>');
		
		if ($meta['bestill']) {
			// wp_die('<xmp>'.print_r('hi', true).'</xmp>');
			
			// if ($redir) $meta['bestill'] = EML_sc::add_site($post[0]->post_name.'-get');

			// if ($meta['qstring']) { 
			// 	if ($meta['pixel']) $meta['pixel'] = EM_list_tracking::pixel($meta['pixel'], $meta['ttemplate']);
			// 	$meta['bestill'] = EM_list_tracking::query($meta['bestill'], $meta['ttemplate']);
			// }

			if ($redir) {
				$pf = get_option('em_lists');
				
				if (isset($pf['redir_pf']) && $pf['redir_pf']) $pf = '-'.ltrim($pf['redir_pf'], '-');
				else $pf = '-get';

				$meta['bestill'] = EM_list_sc::add_site($p->post_name.$pf);
				// wp_die('<xmp>'.print_r($meta, true).'</xmp>');
			}

			

			if (isset($meta['qstring']) && $meta['qstring']) { 
				if ($meta['pixel']) $meta['pixel'] = EM_list_tracking::pixel($meta['pixel'], $meta['ttemplate']);
				$meta['bestill'] = EM_list_tracking::query($meta['bestill'], $meta['ttemplate']);
			}
			else $meta['pixel'] = '';


			// image with anchor
			return sprintf('<div class="%s-logo-ls"%s>%s<a target="_blank" rel=noopner href="%s"><img class="%s-image" alt="%s" src="%s"></a></div>',
						wp_kses_post($name),
						$float ? $float : '',
						$meta['pixel'],
						esc_url($meta['bestill']),
						wp_kses_post($name),
						esc_attr($post[0]->post_title),
						esc_url(get_the_post_thumbnail_url($post[0], 'full'))
					);
		}

		// no anchor
		return sprintf('<div class="%s-logo-ls"%s><img class="%s-image" alt="%s" src="%s"></div>',
						wp_kses_post($name),
						$float ? $float : '',
						wp_kses_post($name),
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

		$p = $post[0];

		$meta = get_post_meta($post[0]->ID, $name.'_data');

		if (!is_array($meta)) return;

		$meta = $meta[0];

		if (!$meta['bestill']) return;

		$redir = get_post_meta($post[0]->ID, $name.'_redirect');
		if (isset($redir[0])) $redir = $redir[0];

			// wp_die('<xmp>'.print_r($meta, true).'</xmp>');
		
		$float = false;
		if ($atts['float']) 
			switch ($atts['float']) {
				case 'left': $float = ' style="float: left; margin-right: 3rem;"'; break;
				case 'right': $float = ' style="float: right; margin-left: 3rem;"'; break;
			}

		// if ($redir) $meta['bestill'] = EM_list_sc::add_site($post[0]->post_name.'-get');

		// if ($meta['qstring']) { 
		// 	if ($meta['pixel']) $meta['pixel'] = EM_list_tracking::pixel($meta['pixel'], $meta['ttemplate']);
		// 	$meta['bestill'] = EM_list_tracking::query($meta['bestill'], $meta['ttemplate']);
		// }

		if ($redir) {
			// wp_die('<xmp>'.print_r('hi', true).'</xmp>');
			// 
			$pf = get_option('em_lists');
			
			if (isset($pf['redir_pf']) && $pf['redir_pf']) $pf = '-'.ltrim($pf['redir_pf'], '-');
			else $pf = '-get';

			$meta['bestill'] = EM_list_sc::add_site($p->post_name.$pf);
		}


		if (isset($meta['qstring']) && $meta['qstring']) { 
			if ($meta['pixel']) $meta['pixel'] = EM_list_tracking::pixel($meta['pixel'], $meta['ttemplate']);
			$meta['bestill'] = EM_list_tracking::query($meta['bestill'], $meta['ttemplate']);
		}
		else $meta['pixel'] = '';

		return sprintf('<div class="%s-order-solo %s-order-container"%s>%s<a target="_blank" rel=noopener class="%s-order-link %s-link" href="%s">%s</a></div>',
						$name,
						$name,
						$float ? $float : '',
						$meta['pixel'],
						$name,
						$name,
						esc_url($meta['bestill']),
						'<svg class="'.$name.'-svg" version="1.1" x="0px" y="0px" width="26px" height="20px" viewBox="0 0 26 20" enable-background="new 0 0 24 24" xml:space="preserve"><path fill="none" d="M0,0h24v24H0V0z"/><path class="emlanlist-thumb" d="M1,21h4V9H1V21z M23,10c0-1.1-0.9-2-2-2h-6.31l0.95-4.57l0.03-0.32c0-0.41-0.17-0.79-0.44-1.06L14.17,1L7.59,7.59C7.22,7.95,7,8.45,7,9v10c0,1.1,0.9,2,2,2h9c0.83,0,1.54-0.5,1.84-1.22l3.02-7.05C22.95,12.5,23,12.26,23,12V10z"/></svg>'
					);
	}

	public static function add_site($url) {
		if (strpos($url, 'http')) return $url;
		return get_site_url().'/'.$url;
	}

}