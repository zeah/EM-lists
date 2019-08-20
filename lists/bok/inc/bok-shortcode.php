<?php 

/***
	for redirection:

	legg til hidden input med post id

	legg til hidden input med redirection = true

	når redirection == true
	hent meta[bestill] med post id

	redirekt videre med kopiert query string
*/

/**
 * WP Shortcodes
 */
final class Bok_shortcode {
	/* singleton */
	private static $instance = null;

	private $button_text = 'Bestill nå';

	// private $name = 'bokliste';

	// public $pixels = [];

	public static function get_instance() {
		if (self::$instance === null) self::$instance = new self();

		return self::$instance;
	}

	private function __construct() {
		$data = get_option('em_lists');
		$e = BOK.'_text';
		$this->button_text = (isset($data[$e]) && $data[$e]) ? $data[$e] : 'Bestill nå';

		$this->wp_hooks();
	}


	/**
	 * hooks for wp
	 */
	private function wp_hooks() {

		// loan list
		if (!shortcode_exists('bok')) add_shortcode('bok', [$this, 'add_shortcode']);
		else add_shortcode('embok', [$this, 'add_shortcode']);

		add_filter('search_first', [$this, 'add_serp']);
	}



	/**
	 * returns a list of loans
	 */
	public function add_shortcode($atts, $content = null) {
		return EM_list_parts::ab(BOK, $this, $atts, $content);
	}



	/**
	 * Default shortcode content
	 * 
	 * @param [type] $atts    [description]
	 * @param [type] $content [description]
	 */
	public function add_shortcode1($atts, $content = null) {
		add_action('wp_footer', ['EM_list_parts', 'add_ga'], 0);
		add_action('wp_enqueue_scripts', [$this, 'add_css']);

		return $this->get_html(EM_list_sc::posts(BOK.'liste', 'bok', $atts, $content), $atts, false);
	}



	/**
	 * alternate shortcode content when ab-testing is live
	 * 
	 * @param [type] $atts    [description]
	 * @param [type] $content [description]
	 */
	public function add_shortcode2($atts, $content = null) {
		add_action('wp_enqueue_scripts', [$this, 'add_css']);
		add_action('wp_footer', ['EM_list_parts', 'add_ga'], 0);

		$ab = get_option('em_lists');
		$e = BOK.'list_ab';
		$ab = (isset($ab[$e]) && $ab[$e]) ? $ab[$e] : false;

		return $this->get_html(EM_list_sc::posts(BOK.'liste', 'bok', $atts, $content), $atts, $ab);
	}





	/**
	 * adding sands to head
	 */
	public function add_css() {
        wp_enqueue_style(BOK.'-style', BOK_PLUGIN_URL.'assets/css/pub/em-bok.css', [], '1.0.3');
	}


	/**
	 * returns the html of a list of loans
	 * @param  WP_Post $posts a wp post object
	 * @return [html]        html list of loans
	 */
	private function get_html($posts, $atts = null, $ab = false) {
		// return $ab;
		$html = '<ul class="bok-ul">';

		foreach ($posts as $p) {

			$meta = get_post_meta($p->ID, BOK.'liste_data');

			// skip if no meta found
			if (isset($meta[0])) $meta = $meta[0];
			else continue;

			// grid container
			$meta = $this->esc_kses($meta);

			$meta['post_name'] = $p->post_name;

			$title = $p->post_title;
			if (isset($meta['ctitle']) && $meta['ctitle']) $title = $meta['ctitle'];

			$logo_meta = $meta;
			unset($logo_meta['bestill']);
			$logo = EM_list_parts::logo([
				'image' => wp_kses_post(get_the_post_thumbnail_url($p,'post-thumbnail')),
				'meta' => $logo_meta,
				'title' => 'Les mer',
				'ab' => $ab,
				'name' => BOK
			]);

			$image = EM_list_parts::logo([
				'image' => $meta['image'],
				'meta' => $meta,
				'title' => $this->button_text,
				'ab' => $ab,
				'name' => BOK
			]);

			$html .= sprintf(
				'<li class="%1$s-list"><form class="%1$s-container" target="_blank" rel=nofollow action="%2$s" method="get">
					<h2 class="%1$s-title">%3$s</h2>
					%4$s
					%5$s
					%6$s
					%7$s
					%8$s
					</form></li>
				',
				BOK, 

				preg_replace('/\?.*$/', '', $meta['bestill']),

				$title,

				(isset($meta['readmore']) && $meta['readmore']) 
					? sprintf('<a class="%s-logo-top" href="%s">%s</a>', BOK, $meta['readmore'], $logo)
					: $logo,

				(isset($meta['info02']) && $meta['info02'])
					? sprintf('<div class="%s-info">%s</div>', BOK, $meta['info02'])
					: '',

				sprintf('<div class="%s-image">%s</div>', BOK, $image), // 6 

				(isset($meta['info03']) && $meta['info03'])
					? sprintf('<div class="%s-verdi">%s</div>', BOK, $meta['info03'])
					: '',

				EM_list_parts::button([
							'name' => BOK,
							'meta' => $meta,
							'ab' => $ab,
							'button_text' => 'Bestill'
						])
			);

			
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

		if ($post->post_type != BOK.'liste') return $data;

		$exclude = get_option(BOK.'liste_exclude');
		if (!is_array($exclude)) $exclude = [];
		if (in_array($post->ID, $exclude)) return $data;

		$exclude_serp = get_option(BOK.'liste_exclude_serp');
		if (!is_array($exclude_serp)) $exclude_serp = [];
		if (in_array($post->ID, $exclude_serp)) return $data;

		$html['html'] = $this->get_html([$post]);

		array_push($data, $html);
		add_action('wp_enqueue_scripts', [$this, 'add_css']);

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