<?php
defined('ABSPATH') or die('Blank Space');

final class EM_list_redirect {
	/* singleton */
	private static $instance = null;

	public static function get_instance() {
		if (self::$instance === null) self::$instance = new self();

		return self::$instance;
	}

	private function __construct() {
		$this->wp_hooks();
	}

	private function wp_hooks() {
		add_action('init', array($this, 'redirect'));
		add_action('post_updated', [$this, 'update'], 10, 3);
	}


	public function update($post_id, $post_new, $post_old) {
		$pt = $post_new->post_type;
		$pnn = $post_new->post_name;
		$pno = $post_old->post_name;
		$bestill = isset($_POST[$pt.'_data']['bestill']) ? $_POST[$pt.'_data']['bestill'] : false;
		$redir = isset($_POST[$pt.'_redirect']) ? $_POST[$pt.'_redirect'] : false;
		$optn = $pt.'_redirect';
		$opt = get_option($optn);

		// redirection off
		if (!$redir) {
			unset($opt[$pno]);
			unset($opt[$pnn]);

			update_option($optn, $opt);
			return;
		}


		// redirection is on
		if ($pno) unset($opt[$pno]);
		$opt[$pnn] = $bestill;

		update_option($optn, $opt);
	}


	/**
	 * redirecting 
	 */
	public function redirect() {

		$lists = get_option('em_lists');

		if (!is_array($lists)) return;

		$postfix = (isset($lists['redir_pf']) && $lists['redir_pf']) ? $lists['redir_pf'] : 'get';
		$postfix = '-'.ltrim($postfix, '-');
		
		foreach ($lists as $li => $st) {
			if ($li == 'redir_pf') continue;
									
			$url = get_option($li.'_redirect');
			if (!is_array($url)) continue;
			
			foreach ($url as $key => $dest) {
				$key = str_replace('/', '\/', $key);

				if (preg_match('/'.$key.$postfix.'((\?.*)|$)/', $_SERVER['REQUEST_URI'])) {
					
					if ($_SERVER['QUERY_STRING']) {
						if (!strpos($dest, '?')) $dest .= '?';
						else $dest .= '&';
					}
					// wp_die('<xmp>'.print_r($li, true).'</xmp>');
					
					$this->ga($li, $dest);

					// $tag = get_option('theme_google_scripts');

					// // adwords is just analytics misspelled
					// if (isset($tag['adwords']) && $tag['adwords'] && strpos($_SERVER['HTTP_HOST'], 'googlebot') === false) { 

					// 	$r_url = $_SERVER['REDIRECT_URL'];

					// 	preg_match('/(?:.*)\/(.*)-.*$/', $r_url, $label);

					// 	if (!$label) preg_match('/(?:.*\/)(.*)/', $r_url, $label);

					// 	$dl = $_SERVER['HTTP_REFERER'];
					// 	$ip = $_SERVER['REMOTE_ADDR'];
					// 	$t = 'event';
					// 	$ec = 'List Plugin Clicks';
					// 	$ea = 'Clicks';
					// 	$el = $label[1];
					// 	$cookie = $_COOKIE['em_cid'] ? $_COOKIE['em_cid'] : '555';

					// 	switch ($li) {

					// 		case 'emlanlist': $ec = 'Lån NO'; break;
					// 		case 'emlanlistse': $ec = 'Lån SE'; break;
					// 		case 'emlanlistdk': $ec = 'Lån DK'; break;
					// 		case 'bokliste': $ec = 'Bokliste'; break;

					// 		default: $ec = 'List Plugin Clicks';
					// 	}
						
					// 	preg_match('/^(?:.*\/\/)(.*?)\/(?:.*)$/', $dest, $m);

					// 	if (isset($m[1])) $ea = $ea . ' to ' . $m[1];
						
					// 	// referrer; page; ip; domain; 
					// 	$content = wp_remote_post('https://www.google-analytics.com/collect', array(
					// 		'method' => 'POST',
					// 		'timeout' => 30,
					// 		'redirection' => 5,
					// 		'httpversion' => '1.0',
					// 		'blocking' => false,
					// 		'headers' => array(),
					// 		'body' => [
					// 			'v' => '1', 
					// 			'tid' => $tag['adwords'], 
					// 			'cid' => $cookie,
					// 			'uip' => $ip, 
					// 			't' => $t, 
					// 			'ec' => $ec, 
					// 			'ea' => $ea, 
					// 			'el' => $el, 
					// 			'dl' => $dl
					// 		],
					// 		'cookies' => array()
					// 		)
					// 	);
					// 	// wp_die('<xmp>'.print_r($content['body'], true).'</xmp>');
					// }
					
					
					
					wp_redirect($dest.$_SERVER['QUERY_STRING']);
					exit;
				}
			}
		}

	}

	private function ga($li, $dest) {
		$tag = get_option('theme_google_scripts');

		// adwords is just analytics misspelled
		if (isset($tag['adwords']) && $tag['adwords'] && $_SERVER['HTTP_REFERER'] && !is_user_logged_in()) { 

			$r_url = $_SERVER['REDIRECT_URL'];

			preg_match('/(?:.*)\/(.*)-.*$/', $r_url, $label);

			if (!$label) preg_match('/(?:.*\/)(.*)/', $r_url, $label);

			$dl = $_SERVER['HTTP_REFERER'];
			$ip = $_SERVER['REMOTE_ADDR'];
			$ua = $_SERVER['HTTP_USER_AGENT'];
			$t = 'event';
			$ec = 'List Plugin Clicks';
			$ea = 'Clicks';
			$el = $label[1];
			$cookie = $_COOKIE['em_cid'] ? $_COOKIE['em_cid'] : rand(10000, 50000);

			switch ($li) {

				case 'emlanlist': $ec = 'Lån NO'; break;
				case 'emlanlistse': $ec = 'Lån SE'; break;
				case 'emlanlistdk': $ec = 'Lån DK'; break;
				case 'bokliste': $ec = 'Bokliste'; break;

				default: $ec = 'List Plugin Clicks';
			}
			
			preg_match('/^(?:.*\/\/)(.*?)\/(?:.*)$/', $dest, $m);

			if (isset($m[1])) $ea = $ea . ' to ' . $m[1];
			
			$content = wp_remote_post('https://www.google-analytics.com/collect', array(
				'method' => 'POST',
				'timeout' => 30,
				'redirection' => 5,
				'httpversion' => '1.0',
				'blocking' => false,
				'headers' => array(),
				'body' => [
					'v' => '1', 
					'tid' => $tag['adwords'], 
					'cid' => $cookie,
					'uip' => $ip,
					'ua' => $ua,
					't' => $t, 
					'ec' => $ec, 
					'ea' => $ea, 
					'el' => $el, 
					'dl' => $dl
				],
				'cookies' => array()
				)
			);
			// wp_die('<xmp>'.print_r($content['body'], true).'</xmp>');
		}
	}



	
}