<?php
/**
 * Render callback for the ec/experience-list block.
 */

$args = [
	'post_type'      => 'experiencia',
	'posts_per_page' => -1,
	'post_status'    => 'publish',
];

if ( function_exists( 'pll_current_language' ) ) {
	$args['lang'] = pll_current_language();
}

$query = new WP_Query( $args );

$schema_data = [
	'@context'        => 'https://schema.org',
	'@type'           => 'ItemList',
	'itemListElement' => [],
];

ob_start();
?>
<div class="ec-experience-list-wrapper"> 
	<!-- Slide Selector (Thumbnails) -->
	<div class="slide-selector">
		<?php
		$count = 1;
		if ( $query->have_posts() ) :
			while ( $query->have_posts() ) : $query->the_post();
				$thumbnail_url = get_the_post_thumbnail_url( get_the_ID(), 'large' );
				?>
				<div class="slide-box" data-slide="<?php echo esc_attr( $count ); ?>"
					style="background-image: url('<?php echo esc_url( $thumbnail_url ); ?>');">
					<div class="slide-box-overlay">
						<h3><?php the_title(); ?></h3>
					</div>
				</div>
				<?php
				$count++;
			endwhile;
			wp_reset_postdata();
		endif;
		?>
	</div>

	<!-- Custom Slider (Content) -->
	<div class="custom-slider">

		<!-- Slide 0: Intro fijo -->
		<div class="slide active" data-slide-index="0">
			<div class="slide-container slide-container--centered">
				<figure class="slide-image">
					<img src="http://localhost:8088/wp-content/uploads/2025/05/nuestras-experiencias.svg" alt="<?php esc_attr_e( 'Nuestras Experiencias', 'experience-crud' ); ?>" style="width: 500px; margin-top: 50px;" />
				</figure>
				<div class="slide-content">
					<p><?php echo esc_html( function_exists( 'pll__' ) ? pll__( 'If you plan to visit Mendoza, we would love to host you in our family winery. Our tours are conducted by specialized guides in small groups, so we encourage you to book a date and time in advance. Please click on the following link to make your reservation. We look forward to meeting you!' ) : __( 'If you plan to visit Mendoza, we would love to host you in our family winery. Our tours are conducted by specialized guides in small groups, so we encourage you to book a date and time in advance. Please click on the following link to make your reservation. We look forward to meeting you!', 'experience-crud' ) ); ?></p>
					<div class="slide-buttons">
						<div class="wp-block-button">
							<a class="wp-block-button__link" href="https://catenazapata.meitre.com/">
								<?php echo esc_html( function_exists( 'pll__' ) ? pll__( 'BOOK RESERVATION' ) : __( 'BOOK RESERVATION', 'experience-crud' ) ); ?>
							</a>
						</div>
					</div>
					<div class="contact-link-wrapper">
						<a href="mailto:turismo@catenazapata.com">
							<?php esc_html_e( 'Contact us: turismo@catenazapata.com', 'experience-crud' ); ?>
						</a>
					</div>
				</div>
			</div>
		</div>

		<?php
		$count = 1;
		$query = new WP_Query( $args );
		if ( $query->have_posts() ) :
			while ( $query->have_posts() ) : $query->the_post();
				$id            = get_the_ID();
				$booking_url   = get_post_meta( $id, 'ec_booking_url', true )   ?: 'https://catenazapata.meitre.com/';
				$contact_email = get_post_meta( $id, 'ec_contact_email', true ) ?: 'turismo@catenazapata.com';

				$schema_data['itemListElement'][] = [
					'@type'    => 'ListItem',
					'position' => $count,
					'item'     => [
						'@type'       => 'Service',
						'name'        => get_the_title(),
						'description' => wp_trim_words( get_the_content(), 20 ),
						'provider'    => [ '@type' => 'Winery', 'name' => 'Catena Zapata' ],
						'url'         => get_permalink(),
					],
				];
				?>
				<div class="slide" data-slide-index="<?php echo esc_attr( $count ); ?>">
					<div class="slide-container">
						<div class="slide-content">

							<h2 class="experience-title"><?php the_title(); ?></h2>

							<div class="experience-body">
								<?php the_content(); ?>
							</div>

							<div class="contact-link-wrapper">
								<a href="mailto:<?php echo esc_attr( $contact_email ); ?>">
									<?php printf( esc_html__( 'Contact us: %s', 'experience-crud' ), esc_html( $contact_email ) ); ?>
								</a>
							</div>

							<div class="slide-buttons">
								<?php if ( $booking_url ) : ?>
									<div class="wp-block-button">
										<a class="wp-block-button__link" href="<?php echo esc_url( $booking_url ); ?>">
											<?php echo esc_html( function_exists( 'pll__' ) ? pll__( 'BOOK RESERVATION' ) : __( 'BOOK RESERVATION', 'experience-crud' ) ); ?>
										</a>
									</div>
								<?php endif; ?>
								<button class="go-back-button"><?php echo esc_html( function_exists( 'pll__' ) ? pll__( 'GO BACK' ) : __( 'GO BACK', 'experience-crud' ) ); ?></button>
							</div>

						</div>
					</div>
				</div>
				<?php
				$count++;
			endwhile;
			wp_reset_postdata();
		endif;
		?>
	</div>
</div>

<script type="application/ld+json">
<?php echo wp_json_encode( $schema_data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT ); ?>
</script>

<script>
document.addEventListener( 'DOMContentLoaded', function () {
	const slideBoxes    = document.querySelectorAll( '.slide-box' );
	const slides        = document.querySelectorAll( '.slide' );
	const goBackButtons = document.querySelectorAll( '.go-back-button' );

	function showSlide( index ) {
		slides.forEach( function ( slide ) {
			slide.classList.remove( 'active' );
			slide.style.display = 'none';
		} );
		slideBoxes.forEach( function ( b ) { b.classList.remove( 'active' ); } );

		if ( slides[ index ] ) {
			slides[ index ].style.display = 'block';
			slides[ index ].offsetHeight;
			slides[ index ].classList.add( 'active' );
		}

		var activeBox = document.querySelector( '.slide-box[data-slide="' + index + '"]' );
		if ( activeBox ) activeBox.classList.add( 'active' );
	}

	slideBoxes.forEach( function ( box ) {
		box.addEventListener( 'click', function () {
			showSlide( parseInt( this.getAttribute( 'data-slide' ) ) );
		} );
	} );

	goBackButtons.forEach( function ( button ) {
		button.addEventListener( 'click', function () {
			showSlide( 0 );
		} );
	} );
} );
</script>
<?php
return ob_get_clean();
