<?php 


final class EM_list_parts {
	private static $instance = null;

	public static function get_instance() {
		if (self::$instance === null) self::$instance = new self();

		return self::$instance;
	} 

	public function __construct() {
	
	}


	public static function button($o = []) {
		global $post;

		// checks meta data
		$m = isset($o['meta']) ? $o['meta'] : '';

		$disabled = false;

		// if no link given return nothing
		if (!isset($m['bestill']) || !$m['bestill']) $disabled = true;

		// auto adds paramters depending on which aff net 
		$m['bestill'] = self::auto_add($m['bestill']);

		// strips search query
		$url = preg_replace('/\?.*$/', '', $m['bestill']);

		// searches search query for cpc
		preg_match('/(gclid|msclkid)=(.*?)(?:&|$)/', $_SERVER['QUERY_STRING'], $match);

		// default values - paramter with default value will not be added
		$clid = '[clid]';
		$source = '[source]';

		// if cpc found in query string
		if (isset($match[2])) {
			$clid = $match[2];

			switch ($match[1]) {
				case 'gclid': $source = 'google'; break;
				case 'msclkid': $source = 'bing'; break;
			}
		}

		// if query string not cpc and cookie with cpc exists
		elseif (isset($_COOKIE['em_clid'])) {
			$clid = $_COOKIE['em_clid'];
			if (isset($_COOKIE['em_source'])) $source = $_COOKIE['em_source'];
		}

		// if adtraction set epi 
		// if hasoffer set 


		// replacing stuff in the url (query string)
		$find = [		'/^.*?(\?|$)/', '/&amp;/', 	'/\[clid\]/', 	'/\[source\]/', '/\[page\]/', 		'/\[site\]/'			];
		$replace = [	'', 			'&', 		$clid, 			$source,		$post->post_name,	$_SERVER['SERVER_NAME']	];
		parse_str(preg_replace($find, $replace, $m['bestill']), $out);

		// turn query string into hidden html inputs
		$inputs = '';
		foreach ($out as $value => $key) {
			if (strpos($key, '[') !== false) continue;
			$inputs .= sprintf('<input type="hidden" name="%s" value="%s">',
								$value, $key);
		}

		// fixes name for class name
		if (!isset($o['name'])) $o['name'] = '';
		else $o['name'] .= '-';

		// thumb's up icon
		$thumb = sprintf('<svg class="%1$ssvg" version="1.1" x="0px" y="0px" width="26px" height="20px" viewBox="0 0 26 20" enable-background="new 0 0 24 24" xml:space="preserve"><path fill="none" d="M0,0h24v24H0V0z"/><path class="%1$sthumb" d="M1,21h4V9H1V21z M23,10c0-1.1-0.9-2-2-2h-6.31l0.95-4.57l0.03-0.32c0-0.41-0.17-0.79-0.44-1.06L14.17,1L7.59,7.59C7.22,7.95,7,8.45,7,9v10c0,1.1,0.9,2,2,2h9c0.83,0,1.54-0.5,1.84-1.22l3.02-7.05C22.95,12.5,23,12.26,23,12V10z"/></svg>',
							$o['name']
						);

		if (isset($o['disable_sub_text'])) $m['bestill_text'] = '';

		// returns order button and order text
		return sprintf(
			'<div class="%2$sbestill">%5$s<button data-name="%7$s" class="%2$slink%9$s" type="submit"%8$s>%3$s%4$s</button><div class="%2$sbestilltext">%6$s</div></div>',

			$url,

			$o['name'],

			$thumb,

			' '.$o['button_text'],

			$inputs,

			isset($m['bestill_text']) ? $m['bestill_text'] : '',

			isset($m['post_name']) ? $m['post_name'] : '',

			$disabled ? ' disabled' : '',

			$disabled ? ' '.$o['name'].'disabled' : ''

		);
	}


	private static function auto_add($url) {
		if (strpos($url, 'adtraction')) return EM_list_parts::adtraction($url);
		if (strpos($url, 'adservice')) return EM_list_parts::adservice($url);
		if (strpos($url, 'hotracker')) return EM_list_parts::hasoffers($url);

		return $url;
	}

	private static function adtraction($url) {
		if (strpos($url, '?') === false) $url .= '?';
		if (strpos($url, 'epi=') === false) return $url.'&epi=[clid]';
											return $url;
	}

	private static function adservice($url) {
		if (strpos($url, '?') === false) $url .= '?';
		if (strpos($url, 'sub=') === false) return $url.'&sub=source:[source]|clid:[clid]';
											return $url;
	}

	private static function hasoffers($url) {
		// TODO redo with http_build_query

		if (strpos($url, '?') === false) $url .= '?';
		if (strpos($url, 'aff_click_id=') === false) $url .= '&aff_click_id=[clid]';
		if (strpos($url, 'source=') === false) $url .= '&source=[site]';
		if (strpos($url, 'aff_sub=') === false) $url .= '&aff_sub=[source]';
		if (strpos($url, 'aff_sub2=') === false) $url .= '&aff_sub2=[page]';

		return $url;
	}


	public static function logo($o = []) {

		if (!isset($o['image']) || !$o['image']) return '';

		if (!isset($o['meta']['bestill']) || !$o['meta']['bestill'])
		return sprintf(
			'<img class="%slogo list-logo%s" src="%s">',
			isset($o['name']) ? $o['name'].'-' : '',
			isset($o['class']) ? ' '.$o['class'] : '',
			$o['image']
		);


		return sprintf(
			'<input title="%s" class="%slogo%s" type="image" src="%s">', 
			isset($o['title']) ? $o['title'] : 'Apply now', 
			isset($o['name']) ? $o['name'].'-' : '', 
			isset($o['class']) ? ' '.$o['class'] : '',
			$o['image']
		);
	}

	// public function landingside($type = null, $atts = []) {
	public static function landingside($o = []) {

		if (!isset($o['type']) || !isset($o['atts'])) return '';

		$atts = $o['atts'];
		$type = $o['type'];

		$button_text = isset($o['button_text']) ? $o['button_text'] : 'Apply Now';

		if (!isset($atts['name']) && !isset($atts[0])) return '';

		if (!isset($atts['name'])) $name = $atts[0];
		else $name = $atts['name'];

		// return '<div>'.$name.'</div>';

		$args = [
			'post_type' 		=> $type,
			'posts_per_page'	=> 1,
			'name' 				=> sanitize_text_field($name)
		];


		// wp_die('<xmp>'.print_r($args, true).'</xmp>');
		
		$post = get_posts($args);

		if (!is_array($post) && !isset($post[0])) return '';

		$p = $post[0];

		$thumbnail = get_the_post_thumbnail_url($p, 'full');

		$meta = get_post_meta($p->ID, $type.'_data');

		if (isset($meta[0])) $meta = $meta[0];
		else return '';

		$html = sprintf(
			'<form class="%s-ls-container" target="_blank" rel=nofollow action="%s" method="get">%s%s</form>',

			$type,

			preg_replace('/\?.*$/', '', $meta['bestill']),

			self::logo(['image' => $thumbnail, 'meta' => $meta, 'name' => $type, 'title' => $button_text]),


			self::button(['name' => $type, 'meta' => $meta, 'button_text' => $button_text])
		);

		return $html;

		// wp_die('<xmp>'.print_r($meta, true).'</xmp>');
		

		// return '<img src="'.get_the_post_thumbnail_url($p, 'full').'">';

		// wp_die('<xmp>'.print_r($post, true).'</xmp>');
		

	}

	public static function gp($name, $type) {

		$posts = get_posts([
			'post_type' => $type,
			'posts_per_page' => 1,
			'name' => sanitize_text_field($name)
		]);

		if (!is_array($posts) || !isset($posts[0])) return false;

		return $posts[0];
	}


}