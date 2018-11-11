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

					
					wp_redirect($dest.$_SERVER['QUERY_STRING']);
					exit;
				}
			}
		}

	}
}