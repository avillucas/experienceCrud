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
						<button class="ec-close-modal" aria-label="<?php _e( 'Cerrar', 'experience-crud' ); ?>">
							<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M18 6L6 18M6 6l12 12"></path></svg>
						</button>
						
						<div class="ec-modal-scrollable">
							<div class="ec-modal-header-visual">
								<?php if ( has_post_thumbnail() ) : ?>
									<div class="ec-modal-hero-image">
										<?php the_post_thumbnail( 'full' ); ?>
									</div>
								<?php endif; ?>
							</div>

							<div class="ec-modal-main-content">
								<h1 class="ec-modal-title"><?php the_title(); ?></h1>
								
								<div class="ec-modal-body-grid">
									<div class="ec-modal-description">
										<?php echo apply_filters( 'the_content', get_the_content() ); ?>
									</div>

									<aside class="ec-modal-sidebar">
										<div class="ec-modal-meta-box">
											<h3><?php _e( 'Detalles', 'experience-crud' ); ?></h3>
											
											<?php if ( $duration ) : ?>
												<div class="ec-meta-row">
													<span class="ec-label"><?php _e( 'Duración:', 'experience-crud' ); ?></span>
													<span class="ec-value"><?php echo esc_html( $duration ); ?> <?php _e( 'minutos', 'experience-crud' ); ?></span>
												</div>
											<?php endif; ?>

											<?php if ( $min_members || $max_members ) : ?>
												<div class="ec-meta-row">
													<span class="ec-label"><?php _e( 'Capacidad:', 'experience-crud' ); ?></span>
													<span class="ec-value">
														<?php 
														if ( $min_members && $max_members ) {
															printf( __( 'Grupos de %1$s a %2$s personas', 'experience-crud' ), esc_html( $min_members ), esc_html( $max_members ) );
														} elseif ( $max_members ) {
															printf( __( 'Hasta %s personas', 'experience-crud' ), esc_html( $max_members ) );
														} else {
															printf( __( 'Mínimo %s personas', 'experience-crud' ), esc_html( $min_members ) );
														}
														?>
													</span>
												</div>
											<?php endif; ?>

											<?php if ( $prices ) : ?>
												<div class="ec-meta-row ec-meta-row--block">
													<span class="ec-label"><?php _e( 'Precios:', 'experience-crud' ); ?></span>
													<div class="ec-value ec-prices-list"><?php echo wpautop( esc_html( $prices ) ); ?></div>
												</div>
											<?php endif; ?>
										</div>

										<div class="ec-modal-actions">
											<?php if ( $booking_url ) : ?>
												<a href="<?php echo esc_url( $booking_url ); ?>" class="ec-button ec-button-primary ec-button-full" target="_blank">
													<?php _e( 'RESERVAR', 'experience-crud' ); ?>
												</a>
											<?php endif; ?>
											
											<?php if ( $contact_email ) : ?>
												<a href="mailto:<?php echo esc_attr( $contact_email ); ?>" class="ec-contact-button">
													<?php _e( 'CONSULTAR POR EMAIL', 'experience-crud' ); ?>
												</a>
											<?php endif; ?>

											<button class="ec-back-button ec-close-modal-trigger">
												<?php _e( 'VOLVER', 'experience-crud' ); ?>
											</button>
										</div>
									</aside>
								</div>
							</div>
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
    const closeButtons = document.querySelectorAll('.ec-close-modal, .ec-close-modal-trigger');

    openButtons.forEach(btn => {
        btn.addEventListener('click', () => {
            const id = btn.getAttribute('data-id');
            const dialog = document.getElementById('ec-modal-' + id);
            if (dialog) {
                dialog.showModal();
                document.body.style.overflow = 'hidden'; // Prevent scroll
            }
        });
    });

    closeButtons.forEach(btn => {
        btn.addEventListener('click', () => {
            const dialog = btn.closest('dialog');
            if (dialog) {
                dialog.close();
                document.body.style.overflow = ''; // Restore scroll
            }
        });
    });

    // Close on backdrop click
    document.querySelectorAll('dialog.ec-experience-modal').forEach(dialog => {
        dialog.addEventListener('click', (e) => {
            if (e.target === dialog) {
                dialog.close();
                document.body.style.overflow = '';
            }
        });
    });
});
</script>
<?php
return ob_get_clean();
