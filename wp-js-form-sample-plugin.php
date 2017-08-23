<?php
/**
 * Plugin Name: WP JS Form Sample Plugin
 * Plugin URI: https://github.com/danieliser/WP-JS-Form-Sample-Plugin
 * Description:
 * Version: 1.0.0
 * Author: Code Atlantic
 * Author URI: http://code-atlantic.com/
 * Text Domain: wp-js-form-sample-plugin
 *
 * Minimum PHP: 5.3
 * Minimum WP: 3.5
 *
 * @author      Daniel Iser
 * @copyright   Copyright (c) 2017, Code Atlantic
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


/**
 * @param $class
 */
function wpjsfsp_autoloader( $class ) {

	// project-specific namespace prefix
	$prefix = 'WPJSFSP\\';

	// base directory for the namespace prefix
	$base_dir = __DIR__ . '/classes/';

	// does the class use the namespace prefix?
	$len = strlen( $prefix );
	if ( strncmp( $prefix, $class, $len ) !== 0 ) {
		// no, move to the next registered autoloader
		return;
	}

	// get the relative class name
	$relative_class = substr( $class, $len );

	// replace the namespace prefix with the base directory, replace namespace
	// separators with directory separators in the relative class name, append
	// with .php
	$file = $base_dir . str_replace( '\\', '/', $relative_class ) . '.php';

	// if the file exists, require it
	if ( file_exists( $file ) ) {
		require_once $file;
	}

}

if ( ! function_exists( 'spl_autoload_register' ) ) {
	include_once __DIR__ . '/includes/compat.php';
}

spl_autoload_register( 'wpjsfsp_autoloader' ); // Register autoloader


/**
 * Class WPJSFSP
 */
class WPJSFSP {

	/**
	 * @var string Plugin Name
	 */
	public static $NAME = 'WP JS Form Sample Plugin';

	/**
	 * @var string Plugin Version
	 */
	public static $VER = '1.0.0';

	/**
	 * @var int DB Version
	 */
	public static $DB_VER = 1;

	/**
	 * @var string Plugin Author
	 */
	public static $AUTHOR = 'Daniel Iser';

	/**
	 * @var string
	 */
	public static $MIN_PHP_VER = '5.3';

	/**
	 * @var string
	 */
	public static $MIN_WP_VER = '3.5';

	/**
	 * @var string Plugin URL
	 */
	public static $URL;

	/**
	 * @var string Plugin Directory
	 */
	public static $DIR;

	/**
	 * @var string Plugin FILE
	 */
	public static $FILE;

	/**
	 * @var string Plugin FILE
	 */
	public static $TEMPLATE_PATH = 'wpjsfsp/';

	/**
	 * @var WPJSFSP $instance
	 */
	private static $instance;

	/**
	 * Get active instance
	 *
	 * @return WPJSFSP
	 */
	public static function instance() {
		if ( ! self::$instance ) {
			self::$instance = new self;
			self::$instance->setup_constants();
			self::$instance->load_textdomain();
			self::$instance->includes();
			self::$instance->init();
		}

		return self::$instance;
	}

	/**
	 * Setup plugin constants
	 */
	private function setup_constants() {
		self::$DIR  = plugin_dir_path( __FILE__ );
		self::$URL  = plugins_url( '/', __FILE__ );
		self::$FILE = __FILE__;
	}

	/**
	 * Include necessary files
	 */
	private function includes() {
		require_once self::$DIR . '/includes/options.php';
	}

	/**
	 * Initialize everything
	 */
	private function init() {
		\WPJSFSP\Options::init();
		\WPJSFSP\Admin::init();
		\WPJSFSP\Conditions::instance();
	}

	/**
	 * Internationalization
	 */
	private function load_textdomain() {
		load_plugin_textdomain( 'wp-js-form-sample-plugin' );
	}

	/**
	 * Get the template path.
	 * @return string
	 */
	public function template_path() {
		return apply_filters( 'wpjsfsp_template_path', self::$TEMPLATE_PATH );
	}
}

/**
 * The main function responsible for returning the one true WPJSFSP
 * Instance to functions everywhere.
 *
 * Use this function like you would a global variable, except without needing
 * to declare the global.
 *
 * Example: <?php $jp_content_control = WPJSFSP(); ?>
 *
 * @since 1.0.0
 * @return object The one true WPJSFSP Instance
 */
function wpjsfsp() {
	return WPJSFSP::instance();
}

// Get Recipe Manager Running
add_action( 'plugins_loaded', 'wpjsfsp', 9 );

/**
 * Plugin Activation hook function to check for Minimum PHP and WordPress versions
 *
 * Cannot use static:: in case php 5.2 is used.
 */
function wpjsfsp_activation_check() {
	global $wp_version;

	if ( version_compare( PHP_VERSION, WPJSFSP::$MIN_PHP_VER, '<' ) ) {
		$flag = 'PHP';
	} elseif ( version_compare( $wp_version, WPJSFSP::$MIN_WP_VER, '<' ) ) {
		$flag = 'WordPress';
	} else {
		return;
	}

	$version = 'PHP' == $flag ? WPJSFSP::$MIN_PHP_VER : WPJSFSP::$MIN_WP_VER;

	// Deactivate automatically due to insufficient PHP or WP Version.
	deactivate_plugins( basename( __FILE__ ) );

	$notice = sprintf( __( 'The %4$s %1$s %5$s plugin requires %2$s version %3$s or greater.', 'wp-js-form-sample-plugin' ), WPJSFSP::$NAME, $flag, $version, "<strong>", "</strong>" );

	wp_die( "<p>$notice</p>", __( 'Plugin Activation Error', 'wp-js-form-sample-plugin' ), array(
		'response'  => 200,
		'back_link' => true,
	) );
}

// Ensure plugin & environment compatibility.
register_activation_hook( __FILE__, 'wpjsfsp_activation_check' );
