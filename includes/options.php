<?php
/**
 * @copyright   Copyright (c) 2017, Code Atlantic
 * @author      Daniel Iser
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Get all forum options.
 *
 * @return mixed
 */
function wpjsfsp_get_options() {
	return \WPJSFSP\Options::get_all();
}

/**
 * Get a forum option.
 *
 * @param string $key
 * @param mixed $default
 *
 * @return mixed
 */
function wpjsfsp_get_option( $key, $default = false ) {
	return \WPJSFSP\wpjsfsp_get_option( $key, $default );
}

/**
 * Update a forum option.
 *
 * @param string $key
 * @param bool $value
 *
 * @return bool
 */
function wpjsfsp_update_option( $key = '', $value = false ) {
	return \WPJSFSP\Options::update( $key, $value );
}

/**
 * Delete a forum option
 *
 * @param string $key
 *
 * @return bool
 */
function wpjsfsp_delete_option( $key = '' ) {
	return \WPJSFSP\Options::delete( $key );
}
