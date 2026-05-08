<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class EC_Metaboxes {

	public function register() {
		add_meta_box(
			'ec_experience_details',
			__( 'Contact & Booking', 'experience-crud' ),
			[ $this, 'render_metabox' ],
			'experiencia',
			'side',
			'high'
		);
	}

	public function render_metabox( $post ) {
		$contact_email = get_post_meta( $post->ID, 'ec_contact_email', true );
		$booking_url   = get_post_meta( $post->ID, 'ec_booking_url', true );

		wp_nonce_field( 'ec_save_metabox', 'ec_metabox_nonce' );
		?>
		<style>
			.ec-admin-field { margin-bottom: 14px; }
			.ec-admin-field label { display: block; font-weight: 600; margin-bottom: 4px; font-size: 12px; }
			.ec-admin-field input { width: 100%; }
		</style>

		<div class="ec-admin-field">
			<label for="ec_contact_email"><?php esc_html_e( 'Contact email', 'experience-crud' ); ?></label>
			<input type="email" id="ec_contact_email" name="ec_contact_email" value="<?php echo esc_attr( $contact_email ); ?>">
		</div>

		<div class="ec-admin-field">
			<label for="ec_booking_url"><?php esc_html_e( 'Booking URL', 'experience-crud' ); ?></label>
			<input type="url" id="ec_booking_url" name="ec_booking_url" value="<?php echo esc_attr( $booking_url ); ?>">
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

		if ( isset( $_POST['ec_contact_email'] ) ) {
			update_post_meta( $post_id, 'ec_contact_email', sanitize_email( $_POST['ec_contact_email'] ) );
		}

		if ( isset( $_POST['ec_booking_url'] ) ) {
			update_post_meta( $post_id, 'ec_booking_url', esc_url_raw( $_POST['ec_booking_url'] ) );
		}
	}
}
