<?php
/**
 * Class file for managing exclusion functionality
 *
 * @package NUSA_Search\Includes
 */

namespace NUSA_Search\Includes;

/**
 * Metabox
 */
class Exclude {
	/**
	 * Instance of this class
	 *
	 * @var boolean
	 */
	public static $instance = false;

	/**
	 * Holds the IDs of excluded objects
	 *
	 * @var array
	 */
	protected $excluded;

	/**
	 * Add all actions & filters
	 */
	public function __construct() {
		add_filter( 'pre_get_posts', [ $this, 'search_filter' ] );
		add_action( 'save_post', [ $this, 'post_save' ] );
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
	 * Filter the excluded objects from the search results
	 *
	 * @param Object $query The WP search query.
	 *
	 * @return Object
	 */
	public function search_filter( $query ) {
		if ( ! $query->is_main_query() || is_admin() || ! is_search() ) {
			return $query;
		}

		if ( ! empty( $this->get_excluded() ) ) {
			$query->set( 'post__not_in', array_merge( [], $this->get_excluded() ) );
		}

		return $query;
	}

	/**
	 * Logic to determine saving the object ID in the excluded IDs option.
	 *
	 * @param Integer $post_ID Post ID of the object being saved.
	 *
	 * @return Integer
	 */
	public function post_save( $post_ID ) {
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		if ( ! current_user_can( 'edit_post', $post_ID ) ) {
			return;
		}

		if (
			! isset( $_POST['nusa_search'], $_POST['nusa_search_metabox'] )
			|| ! wp_verify_nonce( sanitize_key( wp_unslash( $_POST['nusa_search_metabox'] ) ), 'save_search_metabox' )
		) {
			return;
		}

		$nusa_search = array_map( 'sanitize_key', $_POST['nusa_search'] );
		$exclude     = ( isset( $nusa_search['exclude'] ) ) ? filter_var( $nusa_search['exclude'], FILTER_VALIDATE_BOOLEAN ) : false;

		$this->save_post_id_to_excludes( $post_ID, $exclude );
	}

	/**
	 * Middle man function to save a single object ID
	 *
	 * @param Integer $post_ID Post ID of the object to save.
	 * @param boolean $exclude Metadata value of whether or not to exclude the post from search.
	 *
	 * @return void
	 */
	private function save_post_id_to_excludes( $post_ID, bool $exclude ) {
		$this->save_post_ids_to_excludes( [ intval( $post_ID ) ], $exclude );
	}

	/**
	 * Filter through the already excluded files to add or remove the post IDs from the option
	 *
	 * @param Integer $post_ids Array of object IDs to filter.
	 * @param Boolean $exclude  Whether or not to exclude the provided object IDs.
	 *
	 * @return void
	 */
	private function save_post_ids_to_excludes( $post_ids, $exclude ) {
		$exclude  = (bool) $exclude;
		$excluded = $this->get_excluded();

		$excluded = $exclude ? array_unique( array_merge( $excluded, $post_ids ) ) : array_diff( $excluded, $post_ids );

		$this->save_excluded( $excluded );
	}

	/**
	 * Save the array of excluded object IDs to the DB in {$prefix}_options
	 *
	 * @param array $excluded_ids The IDs of posts to be saved for excluding from the search results.
	 *
	 * @return void
	 */
	protected function save_excluded( $excluded_ids ) {
		update_option( 'nusa_search_exclude', $excluded_ids );
		$this->excluded = $excluded_ids;
	}

	/**
	 * Determine whether or not the object is excluded from search.
	 *
	 * @param Int $post_ID The Post ID of the object in question.
	 *
	 * @return boolean
	 */
	public function is_excluded( $post_ID ) {
		return false !== array_search( intval( $post_ID ), $this->get_excluded(), true );
	}

	/**
	 * Fetch the list of excluded objects from the DB in {$prefix}_options
	 *
	 * @return array
	 */
	public function get_excluded() {
		if ( null === $this->excluded ) {
			$this->excluded = get_option( 'nusa_search_exclude' );
			if ( ! is_array( $this->excluded ) ) {
				$this->excluded = [];
			}
		}

		return $this->excluded;
	}
}
