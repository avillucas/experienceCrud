<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class EC_Metaboxes {

	public function register() {
		add_meta_box(
			'ec_experience_details',
			__( 'Detalles de la Experiencia (Catena Zapata Style)', 'experience-crud' ),
			[ $this, 'render_metabox' ],
			'experiencia',
			'normal',
			'high'
		);
	}

	public function render_metabox( $post ) {
		// Recuperar valores actuales
		$duration = get_post_meta( $post->ID, 'ec_duration_min', true );
		$min_members = get_post_meta( $post->ID, 'ec_min_members', true );
		$max_members = get_post_meta( $post->ID, 'ec_max_members', true );
		$prices = get_post_meta( $post->ID, 'ec_prices_list', true );
		$contact_email = get_post_meta( $post->ID, 'ec_contact_email', true );
		$booking_url = get_post_meta( $post->ID, 'ec_booking_url', true );

		wp_nonce_field( 'ec_save_metabox', 'ec_metabox_nonce' );
		?>
		<style>
			.ec-admin-field { margin-bottom: 20px; }
			.ec-admin-field label { display: block; font-weight: bold; margin-bottom: 5px; }
			.ec-admin-field input[type="text"], 
			.ec-admin-field input[type="number"], 
			.ec-admin-field input[type="email"], 
			.ec-admin-field input[type="url"], 
			.ec-admin-field textarea { width: 100%; }
		</style>
		
		<div class="ec-admin-fields">
			<div class="ec-admin-field">
				<label for="ec_duration_min"><?php _e( 'Duración (minutos)', 'experience-crud' ); ?></label>
				<input type="number" id="ec_duration_min" name="ec_duration_min" value="<?php echo esc_attr( $duration ); ?>">
			</div>

			<div class="ec-admin-field" style="display: flex; gap: 20px;">
				<div style="flex: 1;">
					<label for="ec_min_members"><?php _e( 'Mínimo Integrantes', 'experience-crud' ); ?></label>
					<input type="number" id="ec_min_members" name="ec_min_members" value="<?php echo esc_attr( $min_members ); ?>">
				</div>
				<div style="flex: 1;">
					<label for="ec_max_members"><?php _e( 'Máximo Integrantes', 'experience-crud' ); ?></label>
					<input type="number" id="ec_max_members" name="ec_max_members" value="<?php echo esc_attr( $max_members ); ?>">
				</div>
			</div>

			<div class="ec-admin-field">
				<label for="ec_prices_list"><?php _e( 'Lista de Precios', 'experience-crud' ); ?></label>
				<textarea id="ec_prices_list" name="ec_prices_list" rows="4"><?php echo esc_textarea( $prices ); ?></textarea>
			</div>

			<div class="ec-admin-field">
				<label for="ec_contact_email"><?php _e( 'Email de Contacto', 'experience-crud' ); ?></label>
				<input type="email" id="ec_contact_email" name="ec_contact_email" value="<?php echo esc_attr( $contact_email ); ?>">
			</div>

			<div class="ec-admin-field">
				<label for="ec_booking_url"><?php _e( 'URL de Reserva (Meitre, etc)', 'experience-crud' ); ?></label>
				<input type="url" id="ec_booking_url" name="ec_booking_url" value="<?php echo esc_attr( $booking_url ); ?>">
			</div>
		</div>
		<?php
	}

	public function save( $post_id ) {
		if ( ! isset( $_POST['ec_metabox_nonce'] ) || ! wp_verify_nonce( $_POST['ec_metabox_nonce'], 'ec_save_metabox' ) ) {
			return;
		}

		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		$fields = [
			'ec_duration_min'  => 'absint',
			'ec_min_members'   => 'absint',
			'ec_max_members'   => 'absint',
			'ec_prices_list'   => 'sanitize_textarea_field',
			'ec_contact_email' => 'sanitize_email',
			'ec_booking_url'   => 'esc_url_raw',
		];

		foreach ( $fields as $key => $sanitize_func ) {
			if ( isset( $_POST[ $key ] ) ) {
				update_post_meta( $post_id, $key, $sanitize_func( $_POST[ $key ] ) );
			}
		}
	}
}
