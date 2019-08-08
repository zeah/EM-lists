<?php 

/**
 * WP Shortcodes
 */
final class Kredittkort_shortcode {
	/* singleton */
	private static $instance = null;

	private $button_text = 'Bestill Kortet';

	public static function get_instance() {
		if (self::$instance === null) self::$instance = new self();

		return self::$instance;
	}

	private function __construct() {
		$data = get_option('em_lists');
		$e = KREDITTKORT.'_text';
		$this->button_text = (isset($data[$e]) && $data[$e]) ? $data[$e] : 'Bestill Kortet';

		$this->wp_hooks();
	}


	/**
	 * hooks for wp
	 */
	private function wp_hooks() {

		// loan list
		if (!shortcode_exists('kredittkort')) add_shortcode('kredittkort', array($this, 'add_shortcode'));
		else add_shortcode('emkredittkort', array($this, 'add_shortcode'));

		// loan thumbnail
		if (!shortcode_exists('kredittkort-bilde')) add_shortcode('kredittkort-bilde', array($this, 'add_shortcode_bilde'));
		else add_shortcode('emkredittkort-bilde', array($this, 'add_shortcode_bilde'));

		// loan button
		if (!shortcode_exists('kredittkort-bestill')) add_shortcode('kredittkort-bestill', array($this, 'add_shortcode_bestill'));
		else add_shortcode('emkredittkort-bestill', array($this, 'add_shortcode_bestill'));

		if (!shortcode_exists('kredittkort-landingside')) add_shortcode('kredittkort-landingside', array($this, 'add_shortcode_landingside'));
		else add_shortcode('emkredittkort-landingside', array($this, 'add_shortcode_landingside'));


		add_filter('search_first', array($this, 'add_serp'));
	}


	/**
	 * returns a list of loans
	 */
	public function add_shortcode($atts, $content = null) {
		add_action('wp_enqueue_scripts', [$this, 'add_css']);
		add_action('wp_footer', ['EM_list_parts', 'add_ga'], 0);
		
		return $this->get_html(EM_list_sc::posts(KREDITTKORT, KREDITTKORT, $atts, $content), $atts);
	}


	public function add_shortcode_landingside($atts, $content = null) {
		if (!isset($atts['name']) || $atts['name'] == '') return;

		add_action('wp_footer', ['EM_list_parts', 'add_ga'], 0);
		add_action('wp_enqueue_scripts', [$this, 'add_css']);

		return EM_list_parts::landingside(['type' => KREDITTKORT, 'atts' => $atts, 'button_text' => $this->button_text]);
	}

	/**
	 * returns only thumbnail from loan (without link)
	 */
	public function add_shortcode_bilde($atts, $content = null) {
		if (!isset($atts['name']) || $atts['name'] == '') return;

		add_action('wp_enqueue_scripts', [$this, 'add_css']);

		return EM_list_parts::logo([
				'image' => wp_kses_post(get_the_post_thumbnail_url(EM_list_parts::gp($atts['name'], KREDITTKORT),'post-thumbnail')),
				'title' => $this->button_text,
				'name' => KREDITTKORT,
				'atts' => $atts
			]);
	}


	/**
	 * returns bestill button only from loan
	 */
	public function add_shortcode_bestill($atts, $content = null) {
		return EM_list_parts::sc_button($atts, KREDITTKORT, $this->button_text, [$this, 'add_css']);
	}


	/**
	 * adding sands to head
	 */
	public function add_css() {

		$vers = '1.0.2';

        wp_enqueue_style(KREDITTKORT.'-style', KREDITTKORT_PLUGIN_URL.'assets/css/pub/em-kredittkort.css', [], $vers, '(min-width: 1025px)');
        wp_enqueue_style(KREDITTKORT.'-mobile', KREDITTKORT_PLUGIN_URL.'assets/css/pub/em-kredittkort-mobile.css', [], $vers, '(max-width: 1024px)');
	}


	/**
	 * returns the html of a list of loans
	 * @param  WP_Post $posts a wp post object
	 * @return [html]        html list of loans
	 */
	private function get_html($posts, $atts) {

		$html = sprintf('<div class="%1$s-kortliste"><ul class="%1$s-ul">', KREDITTKORT);

		foreach ($posts as $p) {
			$meta = get_post_meta($p->ID, KREDITTKORT.'_data');

			// skip if no meta found
			if (isset($meta[0])) $meta = $meta[0];
			else continue;
			
			$meta['post_name'] = $p->post_name;

			$meta = $this->esc_kses($meta);

			$sep = '';
			for ($i = 1; $i <= 6; $i++) 
				$sep .= sprintf('<div class="%1$s-sep %1$s-sep-'.$i.'"></div>', KREDITTKORT);

			$logo_meta = $meta;
			unset($logo_meta['bestill']);

			$logo = EM_list_parts::logo([
				'image' => wp_kses_post(get_the_post_thumbnail_url($p,'post-thumbnail')),
				'meta' => $logo_meta,
				'title' => $this->button_text,
				'name' => KREDITTKORT
			]);

			$find = ['[b]', '[/b]'];
			$replace = ['<b>', '</b>'];

			$info = '';
			$info_2 = '';
			foreach([
						['info01', 'info-0'], 
						['info02', 'info-1'], 
						['info03', 'info-2']
					] as $v) {

				$info .= sprintf(
					'<div class="%1$s-%2$s %1$s-info">%3$s</div>',

					KREDITTKORT,

					$v[1],

					isset($meta[$v[0]]) ? str_replace($find, $replace, sanitize_text_field($meta[$v[0]])) : ''

				);
			}

			foreach([
						['info07', 'aldersgrense'],
						['info05', 'makskreditt'],
						['info06', 'rentefrikreditt'],
						['info08', 'effrente'],
					] as $v) {

				$info_2 .= sprintf(
					'<div class="%1$s-%2$s %1$s-info">%3$s</div>',

					KREDITTKORT,

					$v[1],

					isset($meta[$v[0]]) ? str_replace($find, $replace, sanitize_text_field($meta[$v[0]])) : ''

				);
			}

			$cards = '';
			foreach(wp_get_post_terms($p->ID, KREDITTKORT.'type') as $term) {
				switch ($term->slug) {
					case 'visa': $cards .= sprintf('<img class="%s-card" src="'.KREDITTKORT_PLUGIN_URL.'assets/img/visa-logo.png">', KREDITTKORT); break;
					case 'mastercard': $cards .= sprintf('<img class="%s-card" src="'.KREDITTKORT_PLUGIN_URL.'assets/img/mastercard-logo.png">', KREDITTKORT); break;
				}
			}

			$info_2 .= sprintf(
					'<div class="%1$s-%2$s %1$s-info"><div class="%1$s-%2$s-text">%3$s</div>%4$s</div>',
					
					KREDITTKORT,

					'blurb',

					isset($meta['info04']) ? str_replace($find, $replace, sanitize_text_field($meta['info04'])) : '',

					$cards
						? sprintf('<div class="%1$s-logo-container">%2$s</div>', KREDITTKORT, $cards)
						: ''

			);

			$html .= sprintf(
				'<li class="%1$s-list">
					<form target="_blank" rel=nofollow class="%1$s-container" action="%2$s" method="get">
						%3$s
						<div class="%1$s-title"><h2 class="%1$s-title-header">%4$s</h2></div>
						%5$s
						%6$s
						%7$s
						%8$s
						%9$s
					</form>
				</li>',

				KREDITTKORT,

				preg_replace('/\?.*$/', '', $meta['bestill']),

				$sep,

				(isset($meta['readmore']) && $meta['readmore']) 
					? sprintf('<a class="%s-title-text" href="%s">%s</a>', KREDITTKORT, esc_url($meta['readmore']), $p->post_title)
					: $p->post_title,

				$logo ? sprintf('<div class="%s-thumbnail">%s</div>', KREDITTKORT, $logo) : '',

				$info,

				EM_list_parts::button([
									'name' => KREDITTKORT,
									'meta' => $meta,
									'disable_thumb' => true,
									'button_text' => $this->button_text
								]),


				(isset($meta['readmore']) && $meta['readmore']) 
					? sprintf('<div class="%1$s-lesmer"><a class="%1$s-lesmer-link" href="%2$s">Les mer</a></div>', KREDITTKORT, esc_url($meta['readmore']))
					: '',

				$info_2
			);

		}

		$html .= '</ul></div>';
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

		if ($post->post_type != KREDITTKORT) return $data;

		$exclude = get_option(KREDITTKORT.'_exclude');
		if (!is_array($exclude)) $exclude = [];
		if (in_array($post->ID, $exclude)) return $data;

		$exclude_serp = get_option(KREDITTKORT.'_exclude_serp');
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
		if (!is_array($data)) return wp_kses_post($data);

		$d = [];
		foreach($data as $key => $value)
			$d[$key] = $this->esc_kses($value);

		return $d;
	}
}