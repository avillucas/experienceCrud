<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class EC_I18n {

	public function load_textdomain() {
		load_plugin_textdomain(
			'experience-crud',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);
	}
}
