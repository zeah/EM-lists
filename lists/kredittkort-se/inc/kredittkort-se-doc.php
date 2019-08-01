<?php 


final class Kredittkort_se_doc {
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
		// add_filter('emtheme_doc', array($this, 'add_doc'), 99);
	}
	
	/**
	 * theme filter for populating documentation
	 * 	
	 * @param [array] $data [array passing through theme filter]
	 */
	public function add_doc($data) {
		$data[KREDITTKORT_SE]['title'] = '<h1 id="'.KREDITTKORT_SE.'">Kreditkort Sverige (Plugin)</h1>';

		$data[KREDITTKORT_SE]['index'] = '<li><h2><a href="#'.KREDITTKORT_SE.'">Kreditkort Sverige (Plugin)</a></h2>
											<ul>
												<li><a href="#'.KREDITTKORT_SE.'-shortcode">Shortcode</a></li>
												<li><a href="#'.KREDITTKORT_SE.'-aldri">Aldri vis</a></li>
												<li><a href="#'.KREDITTKORT_SE.'-sort">Sorting order</a></li>
												<li><a href="#'.KREDITTKORT_SE.'-overview">Overview</a></li>
											</ul>
										</li>';
		$data[KREDITTKORT_SE]['info'] = '<li id="'.KREDITTKORT_SE.'-shortcode"><h2>Shortcodes</h2>
										<ul>
											<li><b>[lan]</b>
											<p>[lan] will show all.</p>
											</li>
											<li><b>[lan name="xx, yy"]</b>
											<p>Shows only the loans that is mentioned in the shortcode.
											<br>The name needs to be the slug-name of the loan.
											<br>Loans are sorted by the position they have in name=""
											<br>eks.: [lan name="lendo-privatlan"] will only show the loan with slug-name "lendo-privatlån.
											<br>[lan name="lendo-privatlan, axo-finans"] will show 2 loans: lendo and axo.</p>
											<li><b>[lan lan="xx"]</b>
											<p>lan must match the slug-name of the lan type.
											<br>The loans are sorted by the sort order given in load edit page for that type.
											<br>Eks: [lan lan="frontpage"] shows all loans with the category "frontpage" in the order of lowest number
											<br>of field "Sort frontpage" has in the load editor page.</p>
											</li>
											</li>
											<li><b>[lan-bilde name="xx"]</b>
											<p>Name is required. Will show the loan\'s thumbnail with a link.
											<br>[kredittkort-bestlil name="xx" source="test"] will append &source=test at the link.</p></li>
											<li><b>[lan-bestill name="xx"]</b>
											<p>Name is required. Will show the loan\'s button.
											<br>[kredittkort-bestlil name="xx" source="test"] will append &source=test at the link.</p></li>
											
										</ul>
										</li>
										<li id="'.KREDITTKORT_SE.'-aldri"><h2>Aldri vis</h2>
										<p>If tagged, then the loan will never appear on the front-end.</p>
										</li>
										</li>
										<li id="'.KREDITTKORT_SE.'-sort"><h2>Sorting order</h2>
										<p>The loans will be shown with the lowest "Sort"-value first.
										<br>When only showing a specific category on loan page, then the sort order column will reflect 
										<br>that category\'s sort order.</p>
										</li>
										<li id="'.KREDITTKORT_SE.'-overview"><h2>Overview</h2>
										<p> The <a target="_blank" href="'.get_site_url().'/wp-admin/edit.php?post_type='.KREDITTKORT_SE.'&page='.KREDITTKORT_SE.'-overview">overview page</a> will show every post and page and whether or not there are
										<br>any kredittkort shortcodes in them.
										<br>You can sort the columns alphabetically</p>
										</li>
										';

		return $data;
	}
}