<?php 


final class EM_list_parts {
	private static $instance = null;

	public static function get_instance() {
		if (self::$instance === null) self::$instance = new self();

		return self::$instance;
	} 

	public function __construct() {
	
	}


	public function button($o = []) {

		$m = isset($o['meta']) ? $o['meta'] : '';

		if (!isset($m['bestill']) || !$m['bestill']) return '';

		$m['bestill'] = $this->auto_add($m['bestill']);

		$url = preg_replace('/\?.*$/', '', $m['bestill']);

		preg_match('/(gclid|msclkid)=(.*?)(?:&|$)/', $_SERVER['QUERY_STRING'], $match);

		$clid = '[clid]';
		$source = '[source]';

		if (isset($match[2])) {
			$clid = $match[2];

			switch ($match[1]) {
				case 'gclid': $source = 'google'; break;
				case 'msclkid': $source = 'bing'; break;
			}

		}
		elseif (isset($_COOKIE['em_clid'])) {
			$clid = $_COOKIE['em_clid'];
			if (isset($_COOKIE['em_source'])) $source = $_COOKIE['em_source'];
		}


		// if (strpos($m['bestill'], 'adtraction') !== false && strpos($m['bestill'], 'epi=') === false) $m['bestill'] .= '&epi=[clid]'; 

		// if adtraction set epi 
		// if hasoffer set 

		$find = [
			'/^.*?(\?|$)/',
			'/&amp;/',
			'/\[clid\]/',
			'/\[source\]/'
		];

		$replace = [
			'',
			'&',
			$clid,
			$source
		];

		parse_str(preg_replace($find, $replace, $m['bestill']), $out);

		$inputs = '';

		foreach ($out as $value => $key) {

			if (strpos($key, '[') !== false) continue;

			$inputs .= sprintf('<input type="hidden" name="%s" value="%s">',
								$value, $key);
		}


		if (!isset($o['name'])) $o['name'] = '';
		else $o['name'] .= '-';


		$thumb = sprintf('<svg class="%1$ssvg" version="1.1" x="0px" y="0px" width="26px" height="20px" viewBox="0 0 26 20" enable-background="new 0 0 24 24" xml:space="preserve"><path fill="none" d="M0,0h24v24H0V0z"/><path class="%1$sthumb" d="M1,21h4V9H1V21z M23,10c0-1.1-0.9-2-2-2h-6.31l0.95-4.57l0.03-0.32c0-0.41-0.17-0.79-0.44-1.06L14.17,1L7.59,7.59C7.22,7.95,7,8.45,7,9v10c0,1.1,0.9,2,2,2h9c0.83,0,1.54-0.5,1.84-1.22l3.02-7.05C22.95,12.5,23,12.26,23,12V10z"/></svg>',
							$o['name']
						);

		// wp_die('<xmp>'.print_r($m, true).'</xmp>');
		if (isset($o['form'])) {
			return sprintf(
				// '<form rel=nofollow target="_blank" action="%1$s" method="get" class="%2$sbestill">%5$s<button data-name="%7$s" class="%2$slink" type="submit">%3$s%4$s</button><div class="%2$sbestilltext">%6$s</div></form>',
				'<div class="%2$sbestill">%5$s<button data-name="%7$s" class="%2$slink" type="submit">%3$s%4$s</button><div class="%2$sbestilltext">%6$s</div></div>',

				$url,

				$o['name'],

				$thumb,

				' '.$o['button_text'],

				$inputs,

				isset($m['bestill_text']) ? $m['bestill_text'] : '',

				isset($m['post_name']) ? $m['post_name'] : ''

			);
		}

		else 
			return sprintf('<div class="%1$s-bestill">
						<button type="submit" class="%1$slink">
							<svg class="%1$ssvg" version="1.1" x="0px" y="0px" width="26px" height="20px" viewBox="0 0 26 20" enable-background="new 0 0 24 24" xml:space="preserve"><path fill="none" d="M0,0h24v24H0V0z"/><path class="%1$sthumb" d="M1,21h4V9H1V21z M23,10c0-1.1-0.9-2-2-2h-6.31l0.95-4.57l0.03-0.32c0-0.41-0.17-0.79-0.44-1.06L14.17,1L7.59,7.59C7.22,7.95,7,8.45,7,9v10c0,1.1,0.9,2,2,2h9c0.83,0,1.54-0.5,1.84-1.22l3.02-7.05C22.95,12.5,23,12.26,23,12V10z"/></svg> Ansök här!
						</button>
					</div>
					<div class="%1$sbestilltext">%2$s</div>',

					$o['name'],

					isset($m['bestill_text']) ? $m['bestill_text'] : 'Søk Nå'
				);

	}


	private function auto_add($url) {
		if (strpos($url, 'adtraction')) return $this->adtraction($url);
		if (strpos($url, 'adservice')) return $this->adservice($url);
		if (strpos($url, 'hasoffers')) return $this->hasoffers($url);

		return $url;
	}

	private function adtraction($url) {

		if (strpos($url, 'epi=') === false) return $url.'&epi=[clid]';

		return $url;
	}

	private function adservice($url) {
		return $url;
	}

	private function hasoffers($url) {
		return $url;
	}


	public function logo($o = []) {

		$img = 'url(\''.$o['image'].'\')';


		// wp_die('<xmp>'.print_r($o['image'], true).'</xmp>');
		return '<button class="emlanlist-logo-button" type="submit" style="background-image: '.$img.';"></button>';
		// wp_die('<xmp>'.print_r($o, true).'</xmp>');
		// return sprintf('<form><input type="image" src="%s"></form>', $o['image']);
		// wp_die('<xmp>'.print_r($o, true).'</xmp>');
	}


}