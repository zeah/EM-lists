<?php 

defined('ABSPATH') or die('Blank Space');

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
		
		if (strpos($url, '?') === false) $url .= '?';
		else $url .= '&';

		$cookie = json_decode(str_replace('\\', '', $_COOKIE['eml_clid']));
		// wp_die('<xmp>'.print_r($cookie, true).'</xmp>');
		
		$cid = $_COOKIE['_ga'] ? $_COOKIE['_ga'] : $_COOKIE['em_cid'];

		$cookie_id = $cookie->id ? $cookie->id : 'none';
		$cookie_source = $cookie->source ? $cookie->source : 'organic';


		parse_str($_SERVER['QUERY_STRING'], $qstring);

		// $cookie_id = 

		if (isset($qstring['gclid'])) {
			$cookie_id = $qstring['gclid'];
			$cookie_source = 'google';
		}
		elseif (isset($qstring['msclkid'])) {
			$cookie_id = $qstring['msclkid'];
			$cookie_source = 'bing';
		}

		// wp_die('<xmp>'.print_r($result, true).'</xmp>');
		

		// if (!$cookie) $cookie = 'none'; // remove clid from $res instead?
		// elseif ($cookie->id) $cookie = $cookie->id; 

		$s = ['{site}', '{page}', '{clid}', '{time}', '{cpc}'];
		$r = [$_SERVER['SERVER_NAME'], $post->post_name, $cookie_id, date('y-m-d-H:ie'), $cookie_source];

		
	    return $url.str_replace($s, $r, $template);
	}

	public static function pixel($url, $template = null) {
		if ($template) $url = EM_list_tracking::query($url, $template);

		return '<img width=0 height=0 src="'.esc_url($url).'" style="position:absolute">';
	}

}