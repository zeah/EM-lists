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

		

		if (!isset($_COOKIE['em_cid'])) setcookie('em_cid', $this->gen_uuid(), (time()+(60*60*24*712)), '/');



		parse_str($_SERVER['QUERY_STRING'], $result);

		$source = false;
		$id = false;

		if (isset($result['gclid'])) {
			$source = 'google';
			$id = $result['gclid'];
		}

		if (isset($result['msclkid'])) {
			$source = 'bing';
			$id = $result['msclkid'];
		}

		if ($source && $id) {
			setcookie('em_clid', $id, time()+60*60*24*90, '/');
			setcookie('em_source', $source, time()+60*60*24*90, '/');
		}

		// wp_die('<xmp>'.print_r($result, true).'</xmp>');

		// if (isset($result['gclid'])) setcookie('eml_clid', json_encode(['id' => $result['gclid'], 'source' => 'google']), time()+60*60*24*90);
		// elseif (isset($result['msclkid'])) setcookie('eml_clid', json_encode(['id' => $result['msclkid'], 'source' => 'bing']), time()+60*60*24*90);

		// add country to cookie
		// http://www.geoplugin.net/json.gp?ip=xx.xx.xx.xx

		// wp_die('<xmp>'.print_r(json_decode($_COOKIE['eml_clid']), true).'</xmp>');
	}


	private function gen_uuid() {
	    return sprintf( '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
	        // 32 bits for "time_low"
	        mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ),

	        // 16 bits for "time_mid"
	        mt_rand( 0, 0xffff ),

	        // 16 bits for "time_hi_and_version",
	        // four most significant bits holds version number 4
	        mt_rand( 0, 0x0fff ) | 0x4000,

	        // 16 bits, 8 bits for "clk_seq_hi_res",
	        // 8 bits for "clk_seq_low",
	        // two most significant bits holds zero and one for variant DCE1.1
	        mt_rand( 0, 0x3fff ) | 0x8000,

	        // 48 bits for "node"
	        mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff )
	    );
	}

}