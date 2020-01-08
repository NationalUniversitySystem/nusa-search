<?php
/**
 * Plugin Name:     NUSA Search
 * Plugin URI:      PLUGIN SITE HERE
 * Description:     Custom plugin to customize a WP site's internal search functionality.
 * Author:          Mike Estrada
 * Author URI:      YOUR SITE HERE
 * Text Domain:     nusa-search
 * Version:         0.1.0
 *
 * @package         NUSA_Search
 */

namespace NUSA_Search;

if ( ! defined( 'WPINC' ) ) {
	die( 'YOU SHALL! NOT! PASS!' );
}

define( 'NUSA_SEARCH_VERSION', '0.1.0' );
define( 'NUSA_SEARCH_URL', plugin_dir_url( __FILE__ ) );
define( 'NUSA_SEARCH_PATH', plugin_dir_path( __FILE__ ) );

use NUSA_Search\Core\Init;

add_action( 'plugins_loaded', function() {
	require_once NUSA_SEARCH_PATH . 'core/autoload.php';

	// Initializing file is in includes/class-init.php. Refer to file for setup.
	Init::singleton();
} );
