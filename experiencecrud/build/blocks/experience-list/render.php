<?php
/**
 * Render callback for the ec/experience-list block.
 */

$args = [
	'post_type'      => 'experiencia',
	'posts_per_page' => -1,
	'post_status'    => 'publish',
];

// Soporte para Polylang
if ( function_exists( 'pll_current_language' ) ) {
	$args['lang'] = pll_current_language();
}

$query = new WP_Query( $args );

if ( ! $query->have_posts() ) {
	return '<p>' . __( 'No se encontraron experiencias.', 'experience-crud' ) . '</p>';
}

ob_start();
?>
<div class="ec-experience-list-wrapper">
	<div class="ec-experience-grid">
		<?php while ( $query->have_posts() ) : $query->the_post(); 
			$id = get_the_ID();
			$duration = get_post_meta( $id, 'ec_duration_min', true );
			$min_members = get_post_meta( $id, 'ec_min_members', true );
			$max_members = get_post_meta( $id, 'ec_max_members', true );
			$prices = get_post_meta( $id, 'ec_prices_list', true );
            $booking_url = get_post_meta( $id, 'ec_booking_url', true );
            $contact_email = get_post_meta( $id, 'ec_contact_email', true );
		?>
			<article class="ec-experience-item" data-id="<?php echo esc_attr( $id ); ?>">
				<div class="ec-experience-image">
					<?php if ( has_post_thumbnail() ) : ?>
						<?php the_post_thumbnail( 'large' ); ?>
					<?php endif; ?>
				</div>
				<div class="ec-experience-content">
					<h2 class="ec-experience-title"><?php the_title(); ?></h2>
					
					<div class="ec-experience-meta-summary">
						<?php if ( $duration ) : ?>
							<span><?php echo esc_html( $duration ); ?> min</span>
						<?php endif; ?>
						<?php if ( $max_members ) : ?>
							<span><?php printf( __( 'Up to %s people', 'experience-crud' ), esc_html( $max_members ) ); ?></span>
						<?php endif; ?>
					</div>

					<div class="ec-experience-excerpt">
						<?php the_excerpt(); ?>
					</div>
					<button class="ec-open-modal wp-element-button" data-id="<?php echo esc_attr( $id ); ?>">
						<?php _e( 'VER MÁS', 'experience-crud' ); ?>
					</button>
				</div>

				<!-- Modal Details (Dialog API) -->
				<dialog id="ec-modal-<?php echo esc_attr( $id ); ?>" class="ec-experience-modal">
					<div class="ec-modal-inner">
						<button class="ec-close-modal" aria-label="Cerrar">&times;</button>
						
						<div class="ec-modal-header">
							<h1 class="ec-modal-title"><?php the_title(); ?></h1>
						</div>

						<div class="ec-modal-body">
							<div class="ec-modal-main-image">
								<?php the_post_thumbnail( 'full' ); ?>
							</div>

							<div class="ec-modal-description">
								<?php the_content(); ?>
							</div>

							<div class="ec-modal-meta">
								<?php if ( $duration ) : ?>
									<div class="ec-meta-item">
										<strong><?php _e( 'Duración:', 'experience-crud' ); ?></strong>
										<span><?php echo esc_html( $duration ); ?> <?php _e( 'minutos', 'experience-crud' ); ?></span>
									</div>
								<?php endif; ?>

								<?php if ( $min_members || $max_members ) : ?>
									<div class="ec-meta-item">
										<strong><?php _e( 'Capacidad:', 'experience-crud' ); ?></strong>
										<span>
											<?php 
											if ( $min_members && $max_members ) {
												printf( __( 'Grupos de %1$s a %2$s personas', 'experience-crud' ), esc_html( $min_members ), esc_html( $max_members ) );
											} elseif ( $max_members ) {
												printf( __( 'Grupos de hasta %s personas', 'experience-crud' ), esc_html( $max_members ) );
											} else {
												printf( __( 'Mínimo %s personas', 'experience-crud' ), esc_html( $min_members ) );
											}
											?>
										</span>
									</div>
								<?php endif; ?>

								<?php if ( $prices ) : ?>
									<div class="ec-meta-item ec-meta-prices">
										<strong><?php _e( 'Precios:', 'experience-crud' ); ?></strong>
										<div class="ec-prices-list"><?php echo wpautop( esc_html( $prices ) ); ?></div>
									</div>
								<?php endif; ?>
							</div>
						</div>

						<div class="ec-modal-footer">
							<?php if ( $booking_url ) : ?>
								<a href="<?php echo esc_url( $booking_url ); ?>" class="ec-button ec-button-primary" target="_blank">
									<?php _e( 'RESERVAR', 'experience-crud' ); ?>
								</a>
							<?php endif; ?>
							
							<?php if ( $contact_email ) : ?>
								<a href="mailto:<?php echo esc_attr( $contact_email ); ?>" class="ec-contact-link">
									<?php printf( __( 'Contáctanos: %s', 'experience-crud' ), esc_html( $contact_email ) ); ?>
								</a>
							<?php endif; ?>
						</div>
					</div>
				</dialog>
			</article>
		<?php endwhile; wp_reset_postdata(); ?>
	</div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const openButtons = document.querySelectorAll('.ec-open-modal');
    const closeButtons = document.querySelectorAll('.ec-close-modal');

    openButtons.forEach(btn => {
        btn.addEventListener('click', () => {
            const id = btn.getAttribute('data-id');
            const dialog = document.getElementById('ec-modal-' + id);
            if (dialog) dialog.showModal();
        });
    });

    closeButtons.forEach(btn => {
        btn.addEventListener('click', () => {
            const dialog = btn.closest('dialog');
            if (dialog) dialog.close();
        });
    });

    // Close on backdrop click
    document.querySelectorAll('dialog.ec-experience-modal').forEach(dialog => {
        dialog.addEventListener('click', (e) => {
            if (e.target === dialog) dialog.close();
        });
    });
});
</script>
<?php
return ob_get_clean();
