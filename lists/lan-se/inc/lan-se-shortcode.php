<?php 

/**
 * WP Shortcodes
 */
final class Lan_se_shortcode {
	/* singleton */
	private static $instance = null;

	private $button_text = 'Ansök här!';

	public static function get_instance() {
		if (self::$instance === null) self::$instance = new self();

		return self::$instance;
	}

	private function __construct() {
		$data = get_option('em_lists');
		$e = EMLAN_SE.'_text';
		$this->button_text = (isset($data[$e]) && $data[$e]) ? $data[$e] : 'Ansök här!';
		
		$this->wp_hooks();
	}


	/**
	 * hooks for wp
	 */
	private function wp_hooks() {

		// loan list
		if (!shortcode_exists('lan')) add_shortcode('lan', array($this, 'add_shortcode'));
		else add_shortcode(EMLAN_SE, array($this, 'add_shortcode'));

		// loan thumbnail
		if (!shortcode_exists('lan-bilde')) add_shortcode('lan-bilde', array($this, 'add_shortcode_bilde'));
		else add_shortcode(EMLAN_SE.'-bilde', array($this, 'add_shortcode_bilde'));

		// loan button
		if (!shortcode_exists('lan-bestill')) add_shortcode('lan-bestill', array($this, 'add_shortcode_bestill'));
		else add_shortcode(EMLAN_SE.'-bestill', array($this, 'add_shortcode_bestill'));

		// button and clickable logo
		if (!shortcode_exists('lan-landingside')) add_shortcode('lan-landingside', array($this, 'add_shortcode_landingside'));
		else add_shortcode(EMLAN_SE.'-landingside', array($this, 'add_shortcode_landingside'));


		add_filter('search_first', array($this, 'add_serp'));
	}


	public function add_shortcode($atts, $content = null) {


		return EM_list_parts::ab(EMLAN_SE, $this, $atts, $content);


		// $opt = get_option('em_lists');


		// if (isset($opt[EMLAN_SE.'_ab']) && $opt[EMLAN_SE.'_ab']) {
		// 	$res = rand(0, 1);

		// 	if (!user_is_logged_in()) {
		// 		if (isset($_COOKIE['ab']) && $_COOKIE['ab']) $res = intval($_COOKIE['ab']);
		// 		setcookie('ab', $res, time() + (86400 * 30), "/");
		// 	}

		// 	switch ($res) {
		// 		case 0: return $this->add_shortcode1($atts, $content);
		// 		default: return $this->add_shortcode2($atts, $content);
		// 	}
		// }

		// return $this->add_shortcode1($atts, $content);
	}


	/**
	 * returns a list of loans
	 */
	public function add_shortcode2($atts, $content = null) {
		add_action('wp_enqueue_scripts', array($this, 'add_css'));
		add_action('wp_footer', ['EM_list_parts', 'add_ga'], 0);

		$ab = get_option('em_lists');
		$e = EMLAN_SE.'_ab';
		$ab = (isset($ab[$e]) && $ab[$e]) ? $ab[$e] : false;

		return $this->get_html(EM_list_sc::posts(EMLAN_SE, 'lan', $atts, $content), $atts, $ab);
	}


	public function add_shortcode1($atts, $content = null) {
		add_action('wp_enqueue_scripts', array($this, 'add_css'));
		add_action('wp_footer', ['EM_list_parts', 'add_ga'], 0);

		return $this->get_html(EM_list_sc::posts(EMLAN_SE, 'lan', $atts, $content), $atts, false);
	}



	/**
	 * returns only thumbnail from loan
	 */
	public function add_shortcode_bilde($atts, $content = null) {
		if (!isset($atts['name']) || $atts['name'] == '') return;

		add_action('wp_enqueue_scripts', [$this, 'add_css']);

		return EM_list_parts::logo([
				'image' => wp_kses_post(get_the_post_thumbnail_url(EM_list_parts::gp($atts['name'], EMLAN_SE),'post-thumbnail')),
				'title' => $this->button_text,
				'name' => EMLAN_SE,
				'atts' => $atts
			]);
	}



	/**
	 * returns bestill button only from loan
	 */
	public function add_shortcode_bestill($atts, $content = null) {
		return EM_list_parts::sc_button($atts, EMLAN_SE, $this->button_text, [$this, 'add_css']);
	}


	
	/**
	 * button and logo with link
	 */
	public function add_shortcode_landingside($atts = [], $content = null) {
		add_action('wp_footer', ['EM_list_parts', 'add_ga'], 0);
		add_action('wp_enqueue_scripts', [$this, 'add_css']);
		return EM_list_parts::landingside(['type' => EMLAN_SE, 'atts' => $atts, 'button_text' => $this->button_text]);
	}



	/**
	 * adding sands to head
	 */
	public function add_css() {
        wp_enqueue_style(EMLAN_SE.'-style', LAN_SE_PLUGIN_URL.'assets/css/pub/em-lanlist-se.css', array(), '1.0.2', '(min-width: 921px)');
        wp_enqueue_style(EMLAN_SE.'-mobile', LAN_SE_PLUGIN_URL.'assets/css/pub/em-lanlist-se-mobile.css', array(), '1.0.2', '(max-width: 920px)');
	}
	


	/**
	 * returns the html of a list of loans
	 * @param  WP_Post $posts a wp post object
	 * @return [html]        html list of loans
	 */
	private function get_html($posts, $atts = null, $ab) {
		$html = '<ul class="'.EMLAN_SE.'-ul">';

		// $opt = get_option('em_lists');
		// $e = EMLAN_SE.'_ab';
		// $ab = (isset($opt[$e]) && $opt[$e]) ? $opt[$e] : false;

		foreach ($posts as $p) {
			$meta = get_post_meta($p->ID, EMLAN_SE.'_data');

			// skip if no meta found
			if (isset($meta[0])) $meta = $meta[0];
			else continue;

			$meta['post_name'] = $p->post_name;


			// grid container
			$html .= sprintf(
				'<li class="%1$s-list"><form class="%1$s-container" target="_blank" rel=nofollow action="%2$s" method="get">',
				EMLAN_SE, 
				preg_replace('/\?.*$/', '', $meta['bestill'])
			);
			

			// sanitize meta
			$meta = $this->esc_kses($meta);

			// title
			$html .= '<div class="'.EMLAN_SE.'-title-container"><a class="'.EMLAN_SE.'-title" href="'.$meta['readmore'].'">'.wp_kses_post($p->post_title).'</a></div>';


			// thumbnail
			$html .= EM_list_parts::logo([
				'image' => wp_kses_post(get_the_post_thumbnail_url($p,'post-thumbnail')),
				'meta' => $meta,
				'title' => $this->button_text,
				'name' => EMLAN_SE

			]);

			$info = [
				[
					'class' => '%1$s-info-container',
					'childs' => [
						'01' => 'en',
						'02' => 'to',
						'03' => 'tre',
						'04' => 'fire',
					]
				],
				[
					'class' => '%1$s-list-container',
					'childs' => [
						'05' => 'fem',
						'06' => 'seks',
						'07' => 'syv',
						'08' => 'atte',
					]
				]
			];

			$text = '';
			foreach ($info as $i) {

				$text .= sprintf('<div class="%s">', $i['class']);

				foreach ($i['childs'] as $key => $value) {
					if (!$meta['info'.$key]) continue;
					
					$text .= sprintf(
						'<div class="%%1$s-info %%1$s-info-%s">%s</div>', 
						$value, 
						str_replace('%', '%%', $meta['info'.$key])
					);
				}

				$text .= '</div>';
			}

			$html .= sprintf($text, EMLAN_SE);

			$html .= sprintf(
				'<div class="%s-end-container">%s%s</div>',

				EMLAN_SE,

				EM_list_parts::dice(EMLAN_SE, $meta['terning']),

				EM_list_parts::button([
							'name' => EMLAN_SE,
							'meta' => $meta,
							'ab' => $ab,
							'button_text' => $this->button_text
						])
			);


			$html .= '</form></li>';
		}

		$html .= '</ul>';

		return $html;
	}


	/**
	 * wp filter for adding to internal serp
	 * array_push to $data
	 * $data['html'] to be printed
	 * 
	 * @param [Array] $data [filter]
	 */
	public function add_serp($data) {
		global $post;

		if ($post->post_type != EMLAN_SE) return $data;

		$exclude = get_option(EMLAN_SE.'_exclude');
		if (!is_array($exclude)) $exclude = [];
		if (in_array($post->ID, $exclude)) return $data;

		$exclude_serp = get_option(EMLAN_SE.'_exclude_serp');
		if (!is_array($exclude_serp)) $exclude_serp = [];
		if (in_array($post->ID, $exclude_serp)) return $data;

		$html['html'] = $this->get_html([$post]);

		array_push($data, $html);
		add_action('wp_enqueue_scripts', array($this, 'add_css'));

		return $data;
	}

	/**
	 * kisses the data
	 * recursive sanitizer
	 * 
	 * @param  Mixed $data Strings or Arrays
	 * @return Mixed       Kissed data
	 */
	private function esc_kses($data) {
		if (!is_array($data)) 
			return wp_kses_post(
				str_replace(
					['[b]', '[/b]', '[i]', '[/i]'], 
					['<b>', '</b>', '<i>', '</i>'], 
					$data)
			);

		$d = [];
		foreach($data as $key => $value)
			$d[$key] = $this->esc_kses($value);

		return $d;
	}
}