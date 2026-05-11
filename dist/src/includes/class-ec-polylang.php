<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class EC_Polylang {

	public function register() {
		add_filter( 'pll_get_post_types', [ $this, 'register_post_type' ], 10, 2 );
		add_filter( 'pll_copy_post_metas', [ $this, 'sync_meta_fields' ] );
		add_action( 'pll_init', [ $this, 'register_strings' ] );
	}

	/**
	 * Registra el CPT 'experiencia' en Polylang para que sea traducible.
	 */
	public function register_post_type( $post_types, $is_settings ) {
		$post_types['experiencia'] = 'experiencia';
		return $post_types;
	}

	/**
	 * Sincroniza los campos meta entre traducciones del mismo post.
	 * La URL de reservas y el email de contacto se comparten entre idiomas.
	 */
	public function sync_meta_fields( $metas ) {
		$metas[] = 'ec_booking_url';
		$metas[] = 'ec_contact_email';
		return $metas;
	}

	/**
	 * Registra strings hardcodeadas para traducción desde
	 * Idiomas > Traducciones de cadenas en el panel de Polylang.
	 */
	public function register_strings() {
		pll_register_string( 'ec_intro_text', 'If you plan to visit Mendoza, we would love to host you in our family winery. Our tours are conducted by specialized guides in small groups, so we encourage you to book a date and time in advance. Please click on the following link to make your reservation. We look forward to meeting you!', 'Experience CRUD' );
		pll_register_string( 'ec_book_button', 'BOOK RESERVATION', 'Experience CRUD' );
		pll_register_string( 'ec_go_back_button', 'GO BACK', 'Experience CRUD' );
		pll_register_string( 'ec_contact_label', 'Contact us: %s', 'Experience CRUD' );
	}
}
