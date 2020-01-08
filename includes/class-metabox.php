<?php
/**
 * Class file for managing our custom metabox
 *
 * @package NUSA_Search\Includes
 */

namespace NUSA_Search\Includes;

/**
 * Metabox
 */
class Metabox {
	/**
	 * Instance of this class
	 *
	 * @var boolean
	 */
	public static $instance = false;

	/**
	 * Add all actions & filters
	 */
	public function __construct() {
		add_action( 'add_meta_boxes', [ $this, 'add_metabox' ] );
	}

	/**
	 * Singleton
	 *
	 * Returns a single instance of the current class.
	 */
	public static function singleton() {
		if ( ! self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Register the metabox
	 */
	public static function add_metabox() {
		$current_screen = get_current_screen();
		// Do not show meta box on service pages.
		if ( empty( $current_screen->post_type ) ) {
			return;
		}
		add_meta_box( 'nusa_search', 'Search Options', [ self::class, 'metabox' ], null, 'side' );
	}

	/**
	 * Metabox setup
	 * * Note: Although we are using a metabox, the IDs of excluded objects are stored as a sitewide option.
	 * * "weight" is stored as a metafield though
	 *
	 * @param Object $post WP post object currently being edited.
	 * @return void
	 */
	public static function metabox( $post ) {
		wp_nonce_field( 'save_search_metabox', 'nusa_search_metabox' );
		include_once NUSA_SEARCH_PATH . '/views/metabox.php';
	}
}
