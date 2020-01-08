<?php
/**
 * Manage Plugin initialization
 */

namespace NUSA_Search\Core;

/**
 * Init
 */
class Init {
	/**
	 * Instance of this class
	 *
	 * @var boolean
	 */
	public static $instance = false;

	/**
	 * Classes (namespace structure)
	 * In specific order to loop through so things load accordingly.
	 *
	 * @var array
	 */
	private $class_names = [
		'Includes\Metabox',
		'Includes\Exclude',
		'Includes\Weight',
	];

	/**
	 * Using construct method to add any actions and filters
	 */
	public function __construct() {
		$this->initiate_classes();
	}

	/**
	 * Singleton
	 *
	 * Returns a single instance of this class.
	 */
	public static function singleton() {
		if ( ! self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Call our classes so they are instantiated (one level)
	 *
	 * @return void
	 */
	public function initiate_classes() {
		foreach ( $this->class_names as $class_name ) {
			$full_name = 'NUSA_Search\\' . $class_name;

			if ( method_exists( $full_name, 'singleton' ) ) {
				$full_name::singleton();
			}
		}
	}
}
