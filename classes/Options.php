<?php
/**
 * @copyright   Copyright (c) 2017, Code Atlantic
 * @author      Daniel Iser
 */

namespace WPJSFSP;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Options
 * @package WPJSFSP
 */
class Options {

	/**
	 * Unique Prefix per plugin.
	 *
	 * @var string
	 */
	public static $_prefix = 'wpjsfsp';

	/**
	 * Keeps static copy of the options during runtime.
	 *
	 * @var null|array
	 */
	private static $_data;

	/**
	 * Initialize Options on run.
	 */
	public static function init() {
		// Set the prefix on init.
		static::$_data = static::get_all();
	}

	/**
	 * Get Settings
	 *
	 * Retrieves all plugin settings
	 *
	 * @return array settings
	 */
	public static function get_all() {
		$settings = get_option( static:: $_prefix . '_settings' );
		if ( ! is_array( $settings ) ) {
			$settings = array();
		}

		return apply_filters( static:: $_prefix . '_get_options', $settings );
	}

	/**
	 * Get an option
	 *
	 * Looks to see if the specified setting exists, returns default if not
	 *
	 * @param string $key
	 * @param bool $default
	 *
	 * @return mixed
	 */
	public static function get( $key = '', $default = false ) {
		$value = isset( static::$_data[ $key ] ) ? static::$_data[ $key ] : $default;

		return apply_filters( static::$_prefix . '_get_option', $value, $key, $default );
	}

	/**
	 * Update an option
	 *
	 * Updates an setting value in both the db and the global variable.
	 * Warning: Passing in an empty, false or null string value will remove
	 *          the key from the _options array.
	 *
	 * @since 1.0.0
	 *
	 * @param string $key The Key to update
	 * @param string|bool|int $value The value to set the key to
	 *
	 * @return boolean True if updated, false if not.
	 */
	public static function update( $key = '', $value = false ) {

		// If no key, exit
		if ( empty( $key ) ) {
			return false;
		}

		if ( empty( $value ) ) {
			$remove_option = static::delete( $key );

			return $remove_option;
		}

		// First let's grab the current settings
		$options = get_option( static:: $_prefix . '_settings' );

		// Let's let devs alter that value coming in
		$value = apply_filters( static::$_prefix . '_update_option', $value, $key );

		// Next let's try to update the value
		$options[ $key ] = $value;
		$did_update      = update_option( static:: $_prefix . '_settings', $options );

		// If it updated, let's update the global variable
		if ( $did_update ) {
			static::$_data[ $key ] = $value;

		}

		return $did_update;
	}

	public static function update_all( $options = array() ) {
		$did_update = update_option( static:: $_prefix . '_settings', $options );

		// If it updated, let's update the global variable
		if ( $did_update ) {
			static::$_data = $options;
		}

		return $did_update;
	}

	/**
	 * Remove an option
	 *
	 * Removes a setting value in both the db and the global variable.
	 *
	 * @since 1.0.0
	 *
	 * @param string $key The Key to delete
	 *
	 * @return boolean True if updated, false if not.
	 */
	public static function delete( $key = '' ) {

		// If no key, exit
		if ( empty( $key ) ) {
			return false;
		}

		// First let's grab the current settings
		$options = get_option( static:: $_prefix . '_settings' );

		// Next let's try to update the value
		if ( isset( $options[ $key ] ) ) {
			unset( $options[ $key ] );
		}

		$did_update = update_option( static:: $_prefix . '_settings', $options );

		// If it updated, let's update the global variable
		if ( $did_update ) {
			static::$_data = $options;
		}

		return $did_update;
	}

}
