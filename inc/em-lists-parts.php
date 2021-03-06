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
		wp_enqueue_script('jquery');

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
		$clid = '';
		$source = 'direct';

		if (isset($_SERVER['HTTP_REFERER'])) {
			if (strpos($_SERVER['HTTP_REFERER'], 'bing') || strpos($_SERVER['HTTP_REFERER'], 'google')) $source = 'organic';
			else $source = 'referral';
		}

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

		if (!$clid) $clid = $source;

		if (!isset($o['ab'])) $o['ab'] = '';

		// replacing stuff in the url (query string)
		$find = [		'/^.*?(\?|$)/', '/&amp;/', 	'/\[clid\]/', 	'/\[source\]/', '/\[page\]/', 		'/\[site\]/',				'/\[ab\]/'			];
		$replace = [	'', 			'&', 		$clid, 			$source,		$post->post_name,	$_SERVER['SERVER_NAME'],	$o['ab']			];
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
		$thumb = sprintf('<svg class="%1$ssvg" version="1.1" x="0px" y="0px" width="26px" height="20px" viewBox="0 0 26 20" enable-background="new 0 0 24 24" xml:space="preserve">
							<path fill="none" d="M0,0h24v24H0V0z"/>
							<path class="%1$sthumb" d="M1,21h4V9H1V21z M23,10c0-1.1-0.9-2-2-2h-6.31l0.95-4.57l0.03-0.32c0-0.41-0.17-0.79-0.44-1.06L14.17,1L7.59,7.59C7.22,7.95,7,8.45,7,9v10c0,1.1,0.9,2,2,2h9c0.83,0,1.54-0.5,1.84-1.22l3.02-7.05C22.95,12.5,23,12.26,23,12V10z"/>
						  </svg> ',
							$o['name']
						);

		if (isset($o['disable_thumb'])) $thumb = '';

		if (isset($o['disable_sub_text'])) $m['bestill_text'] = '';


		// returns order button and order text

		// return '<div><a</div>';
		return sprintf(
			'<div class="%2$sbestill">%5$s<button data-name="%7$s" data-action="%11$s" role="button" class="%2$slink emlist-link%9$s" type="submit"%8$s>%3$s%4$s</button>%6$s%10$s</div>',

			$url,

			$o['name'],

			$thumb,

			do_shortcode($o['button_text']),

			$inputs, // 5

			isset($m['bestill_text']) ? sprintf('<div class="%sbestilltext">%s</div>', $o['name'], $m['bestill_text']) : '', // 

			isset($m['post_name']) ? $m['post_name'] : '', // 

			$disabled ? ' disabled' : '', // 

			$disabled ? ' '.$o['name'].'disabled' : '', // 

			(isset($m['pixel']) && $m['pixel']) // 10
				? sprintf('<img width=0 height=0 src="%s" style="position: absolute">', esc_url($m['pixel']))
				: '',

			(isset($o['ab']) && $o['ab']) ? ' '.$o['ab'] : ''

		);
	}


	private static function auto_add($url) {
		if (strpos($url, 'adtraction')) return self::adtraction($url);
		if (strpos($url, 'adservice')) return self::adservice($url);
		if (strpos($url, 'hotracker')) return self::hasoffers($url);
		if (strpos($url, 'go2cloud')) return self::hasoffers($url);
		if (strpos($url, 'tradetracker')) return self::tradetracker($url);

		return $url;
	}

	private static function adtraction($url) {
		if (strpos($url, '?') === false) $url .= '?';
		if (strpos($url, 'epi=') === false) return $url.'&epi=source:[source]|page:[page]|clid:[clid]|ab:[ab]';
											return $url;
	}

	private static function adservice($url) {
		if (strpos($url, '?') === false) $url .= '?';
		if (strpos($url, 'sub=') === false) return $url.'&sub=source:[source]|page:[page]|clid:[clid]|ab:[ab]';
											return $url;
	}

	private static function hasoffers($url) {
		// TODO redo with http_build_query

		if (strpos($url, '?') === false) $url .= '?';
		if (strpos($url, 'aff_click_id=') === false) $url .= '&aff_click_id=[clid]';
		if (strpos($url, 'source=') === false) $url .= '&source=[site]';
		if (strpos($url, 'aff_sub=') === false) $url .= '&aff_sub=[source]';
		if (strpos($url, 'aff_sub2=') === false) $url .= '&aff_sub2=[page]';
		if (strpos($url, 'aff_sub3=') === false) $url .= '&aff_sub3=[ab]';

		return $url;
	}

	private static function tradetracker($url) {
		return preg_replace('/(^.*r=)(.*$)/', '$1page:[page]|source:[source]|ab:[ab]|clid:[clid]$2', $url);
	}


	public static function logo($o = []) {

		// wp_die('<xmp>'.print_r($o, true).'</xmp>');

		if (!isset($o['image']) || !$o['image']) return '';
		// wp_die('<xmp>'.print_r($o, true).'</xmp>');
		if (!isset($o['meta']['bestill']) || !$o['meta']['bestill'])
			return sprintf(
				'<img class="%slogo list-logo%s"%s src="%s">',
				isset($o['name']) ? $o['name'].'-' : '',
				isset($o['class']) ? ' '.$o['class'] : '',
				isset($o['atts']['style']) ? sprintf(' style="%s"', $o['atts']['style']) : '', 
				$o['image']
			);

		return sprintf(
			'<input title="%s" class="%slogo emlist-link%s" data-name="%s" data-action=" logo%s" type="image" src="%s" data-name="%s">', 
			isset($o['title']) ? $o['title'] : 'Apply now', 
			isset($o['name']) ? $o['name'].'-' : '',
			isset($o['class']) ? ' '.$o['class'] : '',
			isset($o['meta']['post_name']) ? $o['meta']['post_name'] : '',
			isset($o['ab']) ? ' '.$o['ab'] : '', 
			$o['image'],
			isset($o['meta']['post_name']) ? $o['meta']['post_name'] : 'na'
		);
	}

	public static function landingside($o = []) {

		if (!isset($o['type']) || !isset($o['atts'])) return '';

		$atts = $o['atts'];
		$type = $o['type'];
		$button_text = isset($o['button_text']) ? $o['button_text'] : 'Apply Now';

		if (!isset($atts['name']) && !isset($atts[0])) return '';

		if (!isset($atts['name'])) $name = $atts[0];
		else $name = $atts['name'];

		$args = [
			'post_type' 		=> $type,
			'posts_per_page'	=> 1,
			'name' 				=> sanitize_text_field($name)
		];

		$post = get_posts($args);
		if (!is_array($post) && !isset($post[0])) return '';

		$p = $post[0];

		$thumbnail = get_the_post_thumbnail_url($p, 'full');

		$meta = get_post_meta($p->ID, $type.'_data');

		if (isset($meta[0])) $meta = $meta[0];
		else return '';

		$meta['post_name'] = $p->post_name;

		return sprintf(
			'<form class="%s-ls-container" target="_blank" rel=nofollow action="%s" method="get">%s%s</form>',

			$type,

			preg_replace('/\?.*$/', '', $meta['bestill']),

			self::logo(['image' => $thumbnail, 'meta' => $meta, 'name' => $type, 'title' => $button_text]),

			self::button(['name' => $type, 'meta' => $meta, 'button_text' => $button_text])
		);
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


	/**
	* SOLO ORDER BUTTON ONLY
	* no logo
	* use landingside() if you want logo too (with link) 
	*/
	public static function sc_button($atts, $type, $button_text = 'Apply Now', $obj = null) {
		global $post;

		if (!isset($atts['name']) || $atts['name'] == '') return;

		if ($obj && isset($obj[0]) && isset($obj[1])) add_action('wp_enqueue_scripts', [$obj[0], $obj[1]]);
		
		// wp_enqueue_script('jquery');
		add_action('wp_footer', ['EM_list_parts', 'add_ga'], 99);

		$p = self::gp($atts['name'], $type);

		if (!$p) return '';

		$meta = get_post_meta($p->ID, $type.'_data');

		if (!is_array($meta) || !isset($meta[0])) return '';

		$meta = $meta[0];
		$meta['post_name'] = $p->post_name;

		return sprintf(
			'<div class="%1$s-solo-button"><form class="%1$s-container" target="_blank" rel=nofollow action="%2$s" method="get">%3$s</form></div>',
			$type, 
			preg_replace('/\?.*$/', '', $meta['bestill']),
			self::button([
				'name' => $type,
				'meta' => $meta,
				'ab' => '',
				'button_text' => $button_text
			])
		);
	}

	public static function add_ga() {
		// if (is_user_logged_in()) return;
		global $post;

		printf('<script>
			jQuery(function($) {
				console.log(navigator.userAgent);
				$(".emlist-link").click(function(e) {
					try { if ("ga" in window) {
						var tracker = ga.getAll()[0];
					    if (tracker) tracker.send("event", "List Plugin", ("%1$s"+this.getAttribute("data-action")), this.getAttribute("data-name"), 0);

						var id = tracker.get("clientId");
						if (id) $(this).siblings("input[name=epi], input[name=sub], input[name=r]").each(function() { $(this).val($(this).val()+"|ga:"+id) });
					} } catch (e) { console.log(e) }
				});
			});
		</script>', $post->post_name);
	}

	public static function dice($name = null, $eyes = 'seks', $left_color = 'rgb(200,0,0)', $right_color = 'rgb(255,0,0)') {

		if ($name) $name .= '-';

		$no_eyes = false;

		$html = '';
		switch ($eyes) {
			case '6':
			case 'seks':
			$html .= self::eye(11, 25);
			$html .= self::eye(39, 25);

			case '4':
			case 'fire':
			$html .= self::eye(11, 10);
			$html .= self::eye(39, 40);

			case '2':
			case 'to':
			$html .= self::eye(11, 40);
			$html .= self::eye(39, 10);
			break;

			case '5':
			case 'fem':
			$html .= self::eye(10, 10);
			$html .= self::eye(40, 40);

			case '3':
			case 'tre':
			$html .= self::eye(10, 40);
			$html .= self::eye(40, 10);

			case '1':
			case 'en':
			$html .= self::eye(25, 25);
			break;

			default: $no_eyes = true;

		}

		return sprintf(
			'<svg class="%1$sterning"%5$s>
				<defs>
				    <linearGradient id="%1$sgrad" x1="0%%" y1="100%%" x2="100%%" y2="100%%">
				      <stop offset="0%%" style="stop-color:%3$s;stop-opacity:1" />
				      <stop offset="100%%" style="stop-color:%4$s;stop-opacity:1" />
				    </linearGradient>
				</defs>
				<rect class="%1$srect-svg" rx="7" ry="7" fill="url(#%1$sgrad)"/>
				%2$s
			</svg>',

			$name,

			sprintf($html, $name),

			$left_color,

			$right_color,

			$no_eyes ? ' style="opacity: 0"' : ''

		);
	}

	private static function eye($x, $y) {

		return sprintf(
			'<circle class="%%1$scircle-svg" cx="%s" cy="%s" r="5"/>',
			$x,
			$y
		);

	} 

	/**
	 * [ab description]
	 * @param  [type] $name    [description]
	 * @param  [type] $obj     [description]
	 * @param  [type] $atts    [description]
	 * @param  [type] $content [description]
	 * @return [type]          [description]
	 */
	public static function ab($name, $obj, $atts, $content = null) {
		$opt = get_option('em_lists');

		if (!method_exists($obj, 'add_shortcode1')) return $obj->add_shortcode2($atts, $content);
		if (!method_exists($obj, 'add_shortcode2')) return $obj->add_shortcode1($atts, $content);

		if ($name == 'bok') $name = 'boklist';

		if (isset($opt[$name.'_ab']) && $opt[$name.'_ab']) {
			$res = rand(0, 1);

			// always show random page to logged in user
			if (!is_user_logged_in()) {
				if (isset($_COOKIE['ab_'.$name]) && $_COOKIE['ab_'.$name]) $res = intval($_COOKIE['ab_'.$name]);
				setcookie('ab_'.$name, $res, time() + (86400 * 30), "/");
			}

			switch ($res) {
				case 0: return $obj->add_shortcode1($atts, $content);
				default: return $obj->add_shortcode2($atts, $content);
			}
		}

		// ab-testing is disabled
		return $obj->add_shortcode1($atts, $content);	
	}


	/**
	 * [struc_card description]
	 * @param  array  $o [description]
	 * @return [type]    [description]
	 */
	public static function struc_card($o = []) {

		// array part in itemListElement in struc data
		$out = [
			'@type' => 'ListItem',
			'position' => $o['struc_pos'],

			'item' => [
				'@type' => 'CreditCard',
				'url' => $o['list_url'],
			]
		];

		// BRAND
		if (self::c($o, 'brand_name') && self::c($o, 'brand_url'))
			$out['item']['brand'] = [
				'@type' => 'Organization',
				'name' => $o['brand_name'],
				'url' => $o['brand_url']
			];

		// INTEREST RATE
		if (self::c($o, 'interestrate_max_value') || self::c($o, 'interestrate_min_value')) {
			$out['item']['interestRate'] = [
				'@type' => 'QuantitativeValue',
				'unitCode' => 'P1'
			];

			if (self::c($o, 'interestrate_max_value')) $out['item']['interestRate']['maxValue'] = $o['interestrate_max_value'];
			if (self::c($o, 'interestrate_min_value')) $out['item']['interestRate']['minValue'] = $o['interestrate_min_value'];
		}

		// CREDIT AMOUNT
		if (self::c($o, 'amount_max_value'))
			$out['item']['amount'] = [
				'@type' => 'MonetaryAmount',
				'maxValue' => $o['amount_max_value'],
				'currency' => $o['amount_currency'],
			];

		// GRACE PERIOD
		if (self::c($o, 'grace_period')) $out['item']['gracePeriod'] = 'P'.$o['grace_period'].'D';

		// SAME AS LINK
		if (self::c($o, 'same_as')) $out['item']['sameAs'] = $o['same_as'];

		return $out;
	}



	/**
	 * [struc_loan description]
	 * @param  array  $o [description]
	 * @return [type]    [description]
	 */
	public static function struc_loan($o = []) {

		$out = [
			'@type' => 'ListItem',
			'position' => $o['struc_pos'],

			'item' => [
				'@type' => 'LoanOrCredit',
				'url' => $o['list_url'],
			]
		];

		// BRAND
		if (self::c($o, 'brand_name') && self::c($o, 'brand_url'))
			$out['item']['brand'] = [
				'@type' => 'Organization',
				'name' => $o['brand_name'],
				'url' => $o['brand_url']
			];

		// INTEREST RATE
		if (self::c($o, 'interestrate_max_value') || self::c($o, 'interestrate_min_value')) {
			$out['item']['interestRate'] = [
				'@type' => 'QuantitativeValue',
				'unitCode' => 'P1'
			];

			if (self::c($o, 'interestrate_max_value')) $out['item']['interestRate']['maxValue'] = $o['interestrate_max_value'];
			if (self::c($o, 'interestrate_min_value')) $out['item']['interestRate']['minValue'] = $o['interestrate_min_value'];
		}

		// LOAN TERM
		if (self::c($o, 'loanterm_max_value') && self::c($o, 'loanterm_min_value')) {
			$out['item']['loanTerm'] = [
				'@type' => 'QuantitativeValue',
				'minValue' => $o['loanterm_min_value'],
				'maxValue' => $o['loanterm_max_value'],
				'unitCode' => 'ANN'
			];
		}

		// CREDIT AMOUNT
		if (self::c($o, 'amount_max_value'))
			$out['item']['amount'] = [
				'@type' => 'MonetaryAmount',
				'maxValue' => $o['amount_max_value'],
				'minValue' => $o['amount_min_value'],
				'currency' => $o['amount_currency'],
			];

		// SAME AS
		if (self::c($o, 'same_as')) $out['item']['sameAs'] = $o['same_as'];

		return $out;
	}


	public static function ga_event($name, $site, $value, $id) {

		$opt = get_option('em_lists');

		if (!isset($opt['gaid']) || !opt['gaid']) return;

		$data = [
			'method' => 'POST',
			'timeout' => 30,
			'redirection' => 5,
			'httpversion' => '1.0',
			'blocking' => false,
			'headers' => [],
			'body' => [
				'v' => '1', 
				'tid' => $opt['gaid'], 
				'cid' => $id,
				// 'uip' => $ip,
				// 'ua' => $ua,
				't' => 'event', 
				'ec' => 'conversions', 
				'ea' => $site, 
				'el' => $name,
				'ev' => $value 
				// 'dl' => $dl
			],
			'cookies' => []
		];

		print_r($data);

		// $content = wp_remote_post('https://www.google-analytics.com/collect', $data);
	}


	/**
	 */
	public static function bb($html) {
		$find = ['/\[b\]/', '/\[\/b\]/'];
		$replace = ['<b>', '</b>'];
		return preg_replace($find, $replace, $html);
	}

	// checking array members and whether they are true
	private static function c($o, $name) {
		if (isset($o[$name]) && $o[$name]) return true;
		return false;
	}


}