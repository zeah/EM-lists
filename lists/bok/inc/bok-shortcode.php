<?php 

/**
 * WP Shortcodes
 */
final class Bok_shortcode {
	/* singleton */
	private static $instance = null;

	private $pf = '-get';

	private $name = 'bokliste';

	// public $pixels = [];

	public static function get_instance() {
		if (self::$instance === null) self::$instance = new self();

		return self::$instance;
	}

	private function __construct() {
		$this->wp_hooks();
	}


	/**
	 * hooks for wp
	 */
	private function wp_hooks() {

		// loan list
		if (!shortcode_exists('bok')) add_shortcode('bok', array($this, 'add_shortcode'));
		else add_shortcode('embok', array($this, 'add_shortcode'));

		// loan thumbnail
		if (!shortcode_exists('bok-bilde')) add_shortcode('bok-bilde', array($this, 'add_shortcode_bilde'));
		else add_shortcode('embok-bilde', array($this, 'add_shortcode_bilde'));

		// loan button
		if (!shortcode_exists('bok-bestill')) add_shortcode('bok-bestill', array($this, 'add_shortcode_bestill'));
		else add_shortcode('embok-bestill', array($this, 'add_shortcode_bestill'));


		add_filter('search_first', array($this, 'add_serp'));
	}


	/**
	 * returns a list of loans
	 */
	public function add_shortcode($atts, $content = null) {

		add_action('wp_enqueue_scripts', array($this, 'add_css'));
		// wp_die('<xmp>'.print_r($atts, true).'</xmp>');
		
		$pf = get_option('em_lists');
		if (isset($pf['redir_pf']) && $pf['redir_pf']) $this->pf = '-'.ltrim($pf['redir_pf'], '-');

		if (is_array($atts) && array_search('gave', $atts) !== false) return $this->get_landing(EM_list_sc::posts($this->name, 'bok', $atts, $content), $atts);
		

		return $this->get_html(EM_list_sc::posts($this->name, 'bok', $atts, $content), $atts);
	}


	/**
	 * returns only thumbnail from loan
	 */
	public function add_shortcode_bilde($atts, $content = null) {
		if (!isset($atts['name']) || $atts['name'] == '') return;

		add_action('wp_enqueue_scripts', array($this, 'add_css'));

		return EM_list_sc::image($this->name, $atts, $content);
	}


	/**
	 * returns bestill button only from loan
	 */
	public function add_shortcode_bestill($atts, $content = null) {
		if (!isset($atts['name']) || $atts['name'] == '') return;

		add_action('wp_enqueue_scripts', array($this, 'add_css'));

		return EM_list_sc::link($this->name, $atts, $content);
	}


	/**
	 * adding sands to head
	 */
	public function add_css() {
        wp_enqueue_style($this->name.'-style', BOK_PLUGIN_URL.'assets/css/pub/em-bok.css', array(), '1.0.0');
        // wp_enqueue_style($this->name.'-style', BOK_PLUGIN_URL.'assets/css/pub/em-bok.css', array(), '1.0.0', '(min-width: 815px)');
        // wp_enqueue_style($this->name.'-mobile', BOK_PLUGIN_URL.'assets/css/pub/em-bok-mobile.css', array(), '1.0.0', '(max-width: 816px)');
	}


	/**
	 * returns the html of a list of loans
	 * @param  WP_Post $posts a wp post object
	 * @return [html]        html list of loans
	 */
	private function get_html($posts, $atts = null) {
		$html = '<ul class="bok-ul">';

		foreach ($posts as $p) {
			
			$meta = get_post_meta($p->ID, $this->name.'_data');

			// skip if no meta found
			if (isset($meta[0])) $meta = $meta[0];
			else continue;


			$redir = get_post_meta($p->ID, $this->name.'_redirect');
			if (isset($redir[0]) && $redir[0]) $redir = true;
			else $redir = false;
			// if ($redir) wp_die('<xmp>'.print_r($redir, true).'</xmp>');

			// grid container
			$html .= '<li class="bok-container">';
			
			if ($redir) $meta['bestill'] = EM_list_sc::add_site($p->post_name.$this->pf);

			if (isset($meta['qstring']) && $meta['qstring']) { 
				if ($meta['pixel']) $html .= EM_list_tracking::pixel($meta['pixel'], $meta['ttemplate']);
				$meta['bestill'] = EM_list_tracking::query($meta['bestill'], $meta['ttemplate']);
			}

			// sanitize meta
			$meta = $this->esc_kses($meta);

			$title = $p->post_title;

			if (isset($meta['ctitle']) && $meta['ctitle']) $title = $meta['ctitle'];

			$html .= '<h2 class="bok-title">'.$title.'</h2>';

			$thumbnail = get_the_post_thumbnail_url($p, 'full');
			if ($thumbnail) {
				if (isset($meta['readmore']) && $meta['readmore']) $html .= '<a class="bok-logo" href="'.$meta['readmore'].'">';
				
				$html .= '<img src="'.$thumbnail.'">';

				if (isset($meta['readmore']) && $meta['readmore']) $html .= '</a>';
			}
			// $html .= '<a href="''"'

			// $html .= '<div class="emlanlist-logo-container"><a target="_blank" rel="noopener" href="'.$meta['bestill'].'"><img class="emlanlist-logo" src="'.wp_kses_post(get_the_post_thumbnail_url($p, 'full')).'"></a></div>';

			if (isset($meta['info02']) && $meta['info02']) $html .= '<div class="bok-info">'.$meta['info02'].'</div>';
			

			if (isset($meta['bestill']) && $meta['bestill']) $html .= '<a target="_blank" rel=noopener class="bok-gave" href="'.$meta['bestill'].'">';
			if (isset($meta['image']) && $meta['image']) $html .= '<img class="bok-gave-image" src="'.esc_url($meta['image']).'">';
			if (isset($meta['bestill']) && $meta['bestill']) $html .= '</a>';

			if (isset($meta['info03']) && $meta['info03']) $html .= '<div class="bok-verdi">'.$meta['info03'].'</div>';
			if (isset($meta['bestill']) && $meta['bestill']) $html .= '<a target="_blank" rel=noopener class="bok-order" href="'.$meta['bestill'].'">'.((isset($meta['bestill_text']) && $meta['bestill_text']) ? $meta['bestill_text'] : 'Bestill Her').'</a>';

			// terning
			if ($meta['terning'] != 'ingen') {
				$html .= '<svg class="bok-terning">
							<defs>
							    <linearGradient id="bok-grad" x1="0%" y1="100%" x2="100%" y2="100%">
							      <stop offset="0%" style="stop-color:rgb(200,0,0);stop-opacity:1" />
							      <stop offset="100%" style="stop-color:rgb(255,0,0);stop-opacity:1" />
							    </linearGradient>
							  </defs>
							<rect class="bok-rect-svg" rx="7" ry="7" fill="url(#bok-grad)"/>';

				switch ($meta['terning']) {

					case 'seks':
					$html .= '<circle class="bok-circle-svg" cx="11" cy="25" r="5"/>';
					$html .= '<circle class="bok-circle-svg" cx="39" cy="25" r="5"/>';

					case 'fire':
					$html .= '<circle class="bok-circle-svg" cx="11" cy="10" r="5"/>';
					$html .= '<circle class="bok-circle-svg" cx="39" cy="40" r="5"/>';

					case 'to':
					$html .= '<circle class="bok-circle-svg" cx="11" cy="40" r="5"/>';
					$html .= '<circle class="bok-circle-svg" cx="39" cy="10" r="5"/>';
					break;

					case 'fem':
					$html .= '<circle class="bok-circle-svg" cx="10" cy="10" r="5"/>';
					$html .= '<circle class="bok-circle-svg" cx="40" cy="40" r="5"/>';

					case 'tre':
					$html .= '<circle class="bok-circle-svg" cx="10" cy="40" r="5"/>';
					$html .= '<circle class="bok-circle-svg" cx="40" cy="10" r="5"/>';

					case 'en':
					$html .= '<circle class="bok-circle-svg" cx="25" cy="25" r="5"/>';
					break;

				}

				$html .= '</svg>';
			}


			$html .= '</li>';
		}

		$html .= '</ul>';

		return $html;
	}

	private function get_landing($posts, $atts = null) {
		if (!is_array($posts)) return;

		$html = '<ul class="bok-ls-ul">';

		foreach ($posts as $p) {

			$meta = get_post_meta($p->ID, $this->name.'_data');
			if (isset($meta[0])) $meta = $meta[0];
			else continue;

			
			$html .= '<li class="bok-ls-li">';

			$html .= '<div class="bok-ls-left">';
			if (isset($meta['bestill'])) $html .= '<a class="bok-order" target="_blank" rel=noopener href="'.esc_url($meta['bestill']).'">Bestill</a> <h2 class="bok-ls-title">'.$meta['gave_title'].'</h2>';
			if (isset($meta['gave_info'])) $html .= '<div class="bok-ls-info">'.wp_kses_post($meta['gave_info']).'</div>';
			$html .= '</div>';
			if (isset($meta['gave_ls'])) $html .= '<img class="bok-ls-image" alt="Bilde av '.$meta['gave_title'].'" src="'.esc_url($meta['image_ls']).'">';

			$html .= '</li>';
			// gave_info
			// gave_ls

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

		if ($post->post_type != $this->name) return $data;

		$exclude = get_option($this->name.'_exclude');
		if (!is_array($exclude)) $exclude = [];
		if (in_array($post->ID, $exclude)) return $data;

		$exclude_serp = get_option($this->name.'_exclude_serp');
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