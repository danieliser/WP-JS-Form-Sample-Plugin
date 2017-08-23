<?php
/**
 * @copyright   Copyright (c) 2017, Code Atlantic
 * @author      Daniel Iser
 */

namespace WPJSFSP\Admin;

use WPJSFSP;
use WPJSFSP\Schedules;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


class Assets {

	public static function init() {
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'scripts_styles' ) );
	}

	public static function scripts_styles( $hook ) {
		global $post_type;

		// Use minified libraries if SCRIPT_DEBUG is turned off
		$suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

		if ( 'settings_page_wpjsfsp-settings' == $hook ) {

			add_action( 'admin_footer', array( __CLASS__, 'js_wp_editor' ) );
			add_action( 'admin_footer', array( '\\WPJSFSP\Admin\Footer_Templates', 'render' ) );

			wp_enqueue_style( 'wpjsfsp-admin', \WPJSFSP::$URL . 'assets/css/wpjsfsp-admin' . $suffix . '.css', array(
				'dashicons',
				'wp-color-picker',
				'editor-buttons',
			), WPJSFSP::$VER, false );
			wp_enqueue_script( 'wpjsfsp-admin', WPJSFSP::$URL . 'assets/js/wpjsfsp-admin' . $suffix . '.js', array(
				'jquery',
				'underscore',
				'wp-color-picker',
				'wp-util',
				'wplink',
			), WPJSFSP::$VER, true );

			wp_localize_script( 'wpjsfsp-admin', 'wpjsfsp_admin_vars', apply_filters( 'wpjsfsp_admin_vars', array(
				'nonce'                  => wp_create_nonce( 'wpjsfsp-admin-nonce' ),
				'I10n'                   => array(
					'conditions'           => array(
						'not_operand' => array(
							'is'  => __( 'Is', 'wp-js-form-sample-plugin' ),
							'not' => __( 'Not', 'wp-js-form-sample-plugin' ),
						),
					),
					'save'                 => __( 'Save', 'wp-js-form-sample-plugin' ),
					'cancel'               => __( 'Cancel', 'wp-js-form-sample-plugin' ),
					'add'                  => __( 'Add', 'wp-js-form-sample-plugin' ),
					'update'               => __( 'Update', 'wp-js-form-sample-plugin' ),
				),
			) ) );
		}


	}

	/**
	 *    JavaScript Wordpress editor
	 *    Author:        Ante Primorac
	 *    Author URI:    http://anteprimorac.from.hr
	 *    Version:        1.1
	 *    License:
	 *        Copyright (c) 2013 Ante Primorac
	 *        Permission is hereby granted, free of charge, to any person obtaining a copy
	 *        of this software and associated documentation files (the "Software"), to deal
	 *        in the Software without restriction, including without limitation the rights
	 *        to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
	 *        copies of the Software, and to permit persons to whom the Software is
	 *        furnished to do so, subject to the following conditions:
	 *
	 *        The above copyright notice and this permission notice shall be included in
	 *        all copies or substantial portions of the Software.
	 *
	 *        THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
	 *        IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
	 *        FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
	 *        AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
	 *        LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
	 *        OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
	 *        THE SOFTWARE.
	 *    Usage:
	 *        server side(WP):
	 *            js_wp_editor( $settings );
	 *        client side(jQuery):
	 *            $('textarea').wp_editor( options );
	 */
	public static function js_wp_editor() {
		if ( ! class_exists( '\_WP_Editors' ) ) {
			require( ABSPATH . WPINC . '/class-wp-editor.php' );
		}

		$set = \_WP_Editors::parse_settings( 'wpjsfsp_id', array() );

		if ( ! current_user_can( 'upload_files' ) ) {
			$set['media_buttons'] = false;
		}

		if ( $set['media_buttons'] ) {
			wp_enqueue_style( 'buttons' );
			wp_enqueue_script( 'thickbox' );
			wp_enqueue_style( 'thickbox' );
			wp_enqueue_script( 'media-upload' );
			wp_enqueue_script( 'wp-embed' );

			$post = get_post( 1 );
			if ( ! $post && ! empty( $GLOBALS['post_ID'] ) ) {
				$post = $GLOBALS['post_ID'];
			}
			wp_enqueue_media( array(
				'post' => $post,
			) );
		}

		\_WP_Editors::editor_settings( 'wpjsfsp_id', $set );

		wp_localize_script( 'wpjsfsp-admin', 'wpjsfsp_wpeditor_vars', array(
			'url'          => get_home_url(),
			'includes_url' => includes_url(),
		) );
	}

}