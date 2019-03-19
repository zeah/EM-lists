<?php 

/**
 * WP Shortcodes
 */
final class Lan_se_shortcode {
	/* singleton */
	private static $instance = null;

	private $name = 'emlanlistse';
	private $pf = '-get';
	// public $pixels = [];

	public static function get_instance() {
		if (self::$instance === null) self::$instance = new self();

		return self::$instance;
	}

	private function __construct() {
		$pf = get_option('em_lists');
		if (isset($pf['redir_pf']) && $pf['redir_pf']) $this->pf = '-'.ltrim($pf['redir_pf'], '-');

		$this->wp_hooks();
	}


	/**
	 * hooks for wp
	 */
	private function wp_hooks() {

		// loan list
		if (!shortcode_exists('lan')) add_shortcode('lan', array($this, 'add_shortcode'));
		else add_shortcode('emlanlist', array($this, 'add_shortcode'));

		// loan thumbnail
		if (!shortcode_exists('lan-bilde')) add_shortcode('lan-bilde', array($this, 'add_shortcode_bilde'));
		else add_shortcode('emlanlist-bilde', array($this, 'add_shortcode_bilde'));

		// loan button
		if (!shortcode_exists('lan-bestill')) add_shortcode('lan-bestill', array($this, 'add_shortcode_bestill'));
		else add_shortcode('emlanlist-bestill', array($this, 'add_shortcode_bestill'));


		add_filter('search_first', array($this, 'add_serp'));
	}


	/**
	 * returns a list of loans
	 */
	public function add_shortcode($atts, $content = null) {
		add_action('wp_enqueue_scripts', array($this, 'add_css'));

		return $this->get_html(EM_list_sc::posts('emlanlistse', 'lan', $atts, $content), $atts);
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
        wp_enqueue_style($this->name.'-style', LAN_SE_PLUGIN_URL.'assets/css/pub/em-lanlist-se.css', array(), '1.0.2', '(min-width: 801px)');
        wp_enqueue_style($this->name.'-mobile', LAN_SE_PLUGIN_URL.'assets/css/pub/em-lanlist-se-mobile.css', array(), '1.0.2', '(max-width: 800px)');
	}


	/**
	 * returns the html of a list of loans
	 * @param  WP_Post $posts a wp post object
	 * @return [html]        html list of loans
	 */
	private function get_html($posts, $atts = null) {
		$html = '<ul class="emlanlist-ul">';

		foreach ($posts as $p) {
			
			$meta = get_post_meta($p->ID, $this->name.'_data');

			// skip if no meta found
			if (isset($meta[0])) $meta = $meta[0];
			else continue;


			$redir = get_post_meta($p->ID, $this->name.'_redirect');
			if (isset($redir[0]) && $redir[0]) $redir = true;
			else $redir = false;

			// grid container
			$html .= '<li class="emlanlist-container">';
			
			if ($redir) $meta['bestill'] = EM_list_sc::add_site($p->post_name.$this->pf);

			if (isset($meta['qstring']) && $meta['qstring']) { 
				if ($meta['pixel']) $html .= EM_list_tracking::pixel($meta['pixel'], $meta['ttemplate']);
				$meta['bestill'] = EM_list_tracking::query($meta['bestill'], $meta['ttemplate']);
			}

			// sanitize meta
			$meta = $this->esc_kses($meta);

			// title
			$html .= '<div class="emlanlist-title-container"><a class="emlanlist-title" href="'.$meta['readmore'].'">'.wp_kses_post($p->post_title).'</a></div>';

			// thumbnail
			$html .= '<div class="emlanlist-logo-container"><a target="_blank" rel="noopener" href="'.$meta['bestill'].'"><img class="emlanlist-logo" src="'.wp_kses_post(get_the_post_thumbnail_url($p,'post-thumbnail')).'"></a></div>';

			// info container
			$html .= '<div class="emlanlist-info-container">';

			// info 1
			if ($meta['info01']) $html .= '<div class="emlanlist-info emlanlist-info-en">'.$meta['info01'].'</div>';

			// info 2
			if ($meta['info02']) $html .= '<div class="emlanlist-info emlanlist-info-to">'.$meta['info02'].'</div>';

			// info 3
			if ($meta['info03']) $html .= '<div class="emlanlist-info emlanlist-info-tre">'.$meta['info03'].'</div>';

			// info 4
			if ($meta['info04']) $html .= '<div class="emlanlist-info emlanlist-info-fire">'.$meta['info04'].'</div>';

			$html .= '</div>';

			// info list container 
			$html .= '<div class="emlanlist-list-container">';

			// info 5
			if ($meta['info05']) $html .= '<div class="emlanlist-info emlanlist-info-fem">'.$meta['info05'].'</div>';

			// info 6
			if ($meta['info06']) $html .= '<div class="emlanlist-info emlanlist-info-seks">'.$meta['info06'].'</div>';

			// info 7
			if ($meta['info07']) $html .= '<div class="emlanlist-info emlanlist-info-syv">'.$meta['info07'].'</div>';

			// info 8
			if ($meta['info08']) $html .= '<div class="emlanlist-info emlanlist-info-atte">'.$meta['info08'].'</div>';

			$html .= '</div>';

			$html .= '<div class="emlanlist-end-container">';
			// terning
			if ($meta['terning'] != 'ingen') {
				$html .= '<svg class="emlanlist-terning">
							<defs>
							    <linearGradient id="emlanlist-grad" x1="0%" y1="100%" x2="100%" y2="100%">
							      <stop offset="0%" style="stop-color:rgb(200,0,0);stop-opacity:1" />
							      <stop offset="100%" style="stop-color:rgb(255,0,0);stop-opacity:1" />
							    </linearGradient>
							  </defs>
							<rect class="emlanlist-rect-svg" rx="7" ry="7" fill="url(#emlanlist-grad)"/>';

				switch ($meta['terning']) {

					case 'seks':
					$html .= '<circle class="emlanlist-circle-svg" cx="11" cy="25" r="5"/>';
					$html .= '<circle class="emlanlist-circle-svg" cx="39" cy="25" r="5"/>';

					case 'fire':
					$html .= '<circle class="emlanlist-circle-svg" cx="11" cy="10" r="5"/>';
					$html .= '<circle class="emlanlist-circle-svg" cx="39" cy="40" r="5"/>';

					case 'to':
					$html .= '<circle class="emlanlist-circle-svg" cx="11" cy="40" r="5"/>';
					$html .= '<circle class="emlanlist-circle-svg" cx="39" cy="10" r="5"/>';
					break;

					case 'fem':
					$html .= '<circle class="emlanlist-circle-svg" cx="10" cy="10" r="5"/>';
					$html .= '<circle class="emlanlist-circle-svg" cx="40" cy="40" r="5"/>';

					case 'tre':
					$html .= '<circle class="emlanlist-circle-svg" cx="10" cy="40" r="5"/>';
					$html .= '<circle class="emlanlist-circle-svg" cx="40" cy="10" r="5"/>';

					case 'en':
					$html .= '<circle class="emlanlist-circle-svg" cx="25" cy="25" r="5"/>';
					break;

				}

				$html .= '</svg>';
			}

			// bestill 
			$html .= '<div class="emlanlist-bestill-container">';
			// $html .= '<div class="emlanlist-bestill"><a target="_blank" rel="noopener" class="emlanlist-link" href="'.$meta['bestill'].'">Ansök här <svg class="emlanlist-svg" version="1.1" x="0px" y="0px" width="26px" height="20px" viewBox="0 0 26 22" enable-background="new 0 0 24 24" xml:space="preserve"><path fill="none" d="M0,0h24v24H0V0z"/><path opacity="0.87" fill="none" d="M0,0h24v24H0V0z"/><path fill="#fff" d="M18,8h-1V6c0-2.76-2.24-5-5-5S7,3.24,7,6v2H6c-1.1,0-2,0.9-2,2v10c0,1.1,0.9,2,2,2h12c1.1,0,2-0.9,2-2V10C20,8.9,19.1,8,18,8z M9,6c0-1.66,1.34-3,3-3s3,1.34,3,3v2H9V6z M18,20H6V10h12V20z M12,17c1.1,0,2-0.9,2-2c0-1.1-0.9-2-2-2c-1.1,0-2,0.9-2,2C10,16.1,10.9,17,12,17z"/></svg></a></div>';
			$html .= '<div class="emlanlist-bestill"><a target="_blank" rel="noopener" class="emlanlist-link" href="'.$meta['bestill'].'"><svg class="emlanlist-svg" version="1.1" x="0px" y="0px" width="26px" height="20px" viewBox="0 0 26 20" enable-background="new 0 0 24 24" xml:space="preserve"><path fill="none" d="M0,0h24v24H0V0z"/><path class="emlanlist-thumb" d="M1,21h4V9H1V21z M23,10c0-1.1-0.9-2-2-2h-6.31l0.95-4.57l0.03-0.32c0-0.41-0.17-0.79-0.44-1.06L14.17,1L7.59,7.59C7.22,7.95,7,8.45,7,9v10c0,1.1,0.9,2,2,2h9c0.83,0,1.54-0.5,1.84-1.22l3.02-7.05C22.95,12.5,23,12.26,23,12V10z"/></svg> Ansök här!</a></div>';
			$html .= '<div class="emlanlist-bestilltext">'.$meta['bestill_text'].'</div>';
			$html .= '</div>';

			$html .= '</div>'; // end-container

			$html .= '</li>';
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