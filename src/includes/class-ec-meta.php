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
			'ec_booking_url' => [
				'type'        => 'string',
				'description' => 'Booking URL',
				'default'     => 'https://catenazapata.meitre.com/',
				'sanitize'    => 'esc_url_raw',
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
