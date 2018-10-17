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



	public static function do($name, $sc, $atts, $content = null) {
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

}