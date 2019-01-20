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

		$cookie = json_decode(str_replace('\\', '', $_COOKIE['eml_clid']));

		$cid = $_COOKIE['em_cid'] ? $_COOKIE['em_cid'] : '';

		if (!$cookie) $cookie = 'none'; // remove clid from $res instead?
		elseif ($cookie->id) $cookie = $cookie->id; 

		$s = ['{site}', '{page}', '{clid}', '{time}', '{cid}'];
		$r = [$_SERVER['SERVER_NAME'], $post->post_name, $cookie, date('y-m-d-H:ie')];

		
	    return $url.str_replace($s, $r, $template);
	}

	public static function pixel($url, $template = null) {
		if ($template) $url = EM_list_tracking::query($url, $template);

		return '<img width=0 height=0 src="'.esc_url($url).'" style="position:absolute">';
	}

}