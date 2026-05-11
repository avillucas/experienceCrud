<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class EC_Meta {

	public function register() {
		$meta_fields = [
			'ec_contact_email' => [
				'type'        => 'string',
				'description' => 'Contact email',
				'default'     => 'turismo@catenazapata.com',
				'sanitize'    => 'sanitize_email',
			],
			'ec_booking_url'   => [
				'type'        => 'string',
				'description' => 'Booking URL',
				'default'     => 'https://catenazapata.meitre.com/',
				'sanitize'    => 'esc_url_raw',
			],
			'ec_duration_min'  => [
				'type'        => 'integer',
				'description' => 'Duration in minutes',
				'default'     => 60,
				'sanitize'    => 'absint',
			],
			'ec_min_members'   => [
				'type'        => 'integer',
				'description' => 'Minimum members',
				'default'     => 1,
				'sanitize'    => 'absint',
			],
			'ec_max_members'   => [
				'type'        => 'integer',
				'description' => 'Maximum members',
				'default'     => 10,
				'sanitize'    => 'absint',
			],
			'ec_prices_list'   => [
				'type'        => 'string',
				'description' => 'Prices list',
				'default'     => '',
				'sanitize'    => 'sanitize_textarea_field',
			],
		];

		foreach ( $meta_fields as $key => $args ) {
			register_post_meta( 'experiencia', $key, [
				'show_in_rest'      => true,
				'single'            => true,
				'type'              => $args['type'],
				'default'           => $args['default'],
				'auth_callback'     => function() {
					return current_user_can( 'edit_posts' );
				},
				'sanitize_callback' => $args['sanitize'],
			] );
		}
	}
}
