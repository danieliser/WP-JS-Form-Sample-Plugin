<?php
/**
 * @copyright   Copyright (c) 2017, Code Atlantic
 * @author      Daniel Iser
 */
namespace WPJSFSP;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


class Admin {

	public static function init() {
		Admin\Assets::init();
		Admin\Ajax::init();
		Admin\Settings::init();
	}

}
