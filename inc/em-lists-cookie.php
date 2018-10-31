<?php 

defined('ABSPATH') or die('Blank Space');

final class EM_list_cookie {
	/* singleton */
	private static $instance = null;

	public static function get_instance() {
		if (self::$instance === null) self::$instance = new self();

		return self::$instance;
	}

	private function __construct() {
		$this->cookie();
	}



	private function cookie() {

		parse_str($_SERVER['QUERY_STRING'], $result);

		if (isset($result['gclid'])) setcookie('eml_clid', json_encode(['id' => $result['gclid'], 'source' => 'google']), time()+60*60*24*90);
		elseif (isset($result['msclkid'])) setcookie('eml_clid', json_encode(['id' => $result['gclid'], 'source' => 'bing']), time()+60*60*24*90);


		// wp_die('<xmp>'.print_r(json_decode($_COOKIE['eml_clid']), true).'</xmp>');
	}

}