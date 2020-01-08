<?php
/**
 * Class file for managing search weight functionality
 *
 * @package NUSA_Search\Includes
 */

namespace NUSA_Search\Includes;

/**
 * Weight
 */
class Weight {
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
		add_filter( 'pre_get_posts', [ $this, 'search_filter' ] );
		add_filter( 'posts_search_orderby', [ $this, 'add_weight_to_orderby' ], 10, 2 );
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

		$query->set( 'meta_query', [ // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
			'relation' => 'OR',
			[
				'key'     => 'search_weight',
				'compare' => 'EXISTS',
			],
			[
				'key'     => 'search_weight',
				'compare' => 'NOT EXISTS',
			],
		] );

		return $query;
	}

	/**
	 * Add the ORDER BY set up through "posts_search_orderby" hook so that relevance exists.
	 *
	 * @param string   $orderby  The ORDER BY clause.
	 * @param WP_Query $wp_query The current WP_Query instance.
	 *
	 * @return string
	 */
	public function add_weight_to_orderby( $orderby, $wp_query ) {
		if ( ! $wp_query->is_main_query() || is_admin() || ! is_search() || empty( $wp_query->get( 'meta_query' ) ) ) {
			return $orderby;
		}

		global $wpdb;

		$meta_sql_orderby = $wpdb->prefix . 'postmeta.meta_value ASC';

		$orderby = $orderby ? $meta_sql_orderby . ', ' . $orderby : $meta_sql_orderby;

		return $orderby;
	}

	/**
	 * Logic to determine saving the weight metadata.
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
			! isset( $_POST['nusa_search'], $_POST['nusa_search']['weight'], $_POST['nusa_search_metabox'] )
			|| ! wp_verify_nonce( sanitize_key( wp_unslash( $_POST['nusa_search_metabox'] ) ), 'save_search_metabox' )
		) {
			return;
		}

		$weight = ! empty( $_POST['nusa_search']['weight'] ) ? number_format( wp_unslash( $_POST['nusa_search']['weight'] ), 2 ) : '';

		update_post_meta( $post_ID, 'search_weight', $weight );
	}
}
