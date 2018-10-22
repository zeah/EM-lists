<?php 


final class EM_list_tracking {
	/* singleton */
	private static $instance = null;

	public static function get_instance() {
		if (self::$instance === null) self::$instance = new self();

		return self::$instance;
	}

	private function __construct() {
		
	}

	public static function query($url, $template = null) {
		if (!$template) return $url;

		global $post;
		// wp_die('<xmp>'.print_r($post, true).'</xmp>');
		
		if (strpos($url, '?') === false) $url .= '?';

		$cookie = json_decode(str_replace('\\', '', $_COOKIE['eml_clid']));

		if (!$cookie) $cookie = 'none'; // remove clid from $res instead?
		elseif ($cookie->id) $cookie = $cookie->id; 

		$s = ['{site}', '{page}', '{clid}', '{time}'];
		$r = [$_SERVER['SERVER_NAME'], $post->post_name, $cookie, date('y-m-d-H:ie')];

	    // $template = str_replace($s, $r, $template);
		// wp_die('<xmp>'.print_r($url, true).'</xmp>');
		
	    return $url.str_replace($s, $r, $template);

	    // wp_die('<xmp>'.print_r($template, true).'</xmp>');
	    
		// add type=google | type=bing						

		// parse_str($template, $res);
		// foreach($res as $key => $value) {
			// $url = str_replace()
		// }
			// switch ($value) {
			// 	case '{site}': $url .= $key.'='.$_SERVER['SERVER_NAME'].'&'; break;
			// 	case '{page}': $url .= $key.'='.$post_name.'&'; break;
			// 	case '{clid}': $url .= $key.'='.$cookie.'&'; break;
			// 	case '{time}': $url .= $key.'='.date('y-m-d-H:ie').'&';break;
			// 	default: $url .= $key.'='.$value.'&'; // $this->get_time() ?
			// }
			

		// return trim($url, '&');
		
	}

}