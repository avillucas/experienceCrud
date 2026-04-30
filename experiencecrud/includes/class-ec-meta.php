<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class EC_Meta {

	public function register() {
		$meta_fields = [
			'ec_duration_min'  => [ 'type' => 'integer', 'description' => 'Duración en minutos' ],
			'ec_min_members'   => [ 'type' => 'integer', 'description' => 'Mínimo de integrantes' ],
			'ec_max_members'   => [ 'type' => 'integer', 'description' => 'Capacidad máxima' ],
			'ec_prices_list'   => [ 'type' => 'string', 'description' => 'Lista de precios' ],
			'ec_contact_email' => [ 'type' => 'string', 'description' => 'Email de contacto' ],
			'ec_booking_url'   => [ 'type' => 'string', 'description' => 'URL de reserva' ],
		];

		foreach ( $meta_fields as $key => $args ) {
			register_post_meta( 'experiencia', $key, [
				'show_in_rest' => true,
				'single'       => true,
				'type'         => $args['type'],
				'auth_callback' => function() {
					return current_user_can( 'edit_posts' );
				},
				'sanitize_callback' => [ $this, 'sanitize_' . $key ],
			] );
		}
	}

	public function sanitize_ec_duration_min( $value ) {
		return absint( $value );
	}

	public function sanitize_ec_min_members( $value ) {
		return absint( $value );
	}

	public function sanitize_ec_max_members( $value ) {
		return absint( $value );
	}

	public function sanitize_ec_prices_list( $value ) {
		return sanitize_textarea_field( $value );
	}

	public function sanitize_ec_contact_email( $value ) {
		return sanitize_email( $value );
	}

	public function sanitize_ec_booking_url( $value ) {
		return esc_url_raw( $value );
	}
}
