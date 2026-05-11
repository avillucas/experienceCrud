<?php
/**
 * Render callback for ec/experience-slider-list block.
 *
 * @var array    $attributes Block attributes.
 * @var string   $content    Block inner content.
 * @var WP_Block $block      Block instance.
 */

use ExperienceCrud\Infrastructure\WordPress\WordPressExperienceRepository;

$repository  = new WordPressExperienceRepository();
$experiences = $repository->findAll();

$intro_logo    = $attributes['introLogoUrl'] ?? '';
$intro_text    = $attributes['introText'] ?? '';
$intro_booking = $attributes['introBookingUrl'] ?? 'https://catenazapata.meitre.com/';
$intro_email   = $attributes['introEmail'] ?? 'turismo@catenazapata.com';

$t = static function ( $key, $default ) {
	return function_exists( 'pll__' ) ? pll__( $default ) : __( $default, 'experience-crud' );
};

$label_book    = $t( 'book',    'BOOK NOW' );
$label_back    = $t( 'back',    'GO BACK' );
$label_contact = $t( 'contact', 'Contact us:' );

$block_uid = 'ec-sl-' . wp_unique_id();
?>
<div class="ec-sl" id="<?php echo esc_attr( $block_uid ); ?>">

	<!-- ── Selector: thumbnails de cada experiencia ─────────────────── -->
	<div class="ec-sl__selector">
		<?php foreach ( $experiences as $experience ) :
			$thumb = get_the_post_thumbnail_url( $experience->getId(), 'medium' );
		?>
		<div class="ec-sl__thumb"
			data-uid="<?php echo esc_attr( $block_uid ); ?>"
			data-target="<?php echo esc_attr( $block_uid . '-exp-' . $experience->getId() ); ?>"
			<?php if ( $thumb ) : ?>style="background-image: url('<?php echo esc_url( $thumb ); ?>');"<?php endif; ?>>
			<div class="ec-sl__thumb-overlay">
				<h3 class="ec-sl__thumb-title"><?php echo esc_html( $experience->getTitle() ); ?></h3>
			</div>
		</div>
		<?php endforeach; ?>
	</div>

	<!-- ── Slider: intro + un panel por experiencia ─────────────────── -->
	<div class="ec-sl__slider">

		<!-- Slide 0: Intro / Nuestras experiencias -->
		<div class="ec-sl__slide ec-sl__slide--active" id="<?php echo esc_attr( $block_uid . '-intro' ); ?>">
			<div class="ec-sl__slide-inner ec-sl__slide-inner--centered">
				<?php if ( $intro_logo ) : ?>
				<figure class="ec-sl__intro-logo">
					<img src="<?php echo esc_url( $intro_logo ); ?>" alt="" />
				</figure>
				<?php endif; ?>

				<?php if ( $intro_text ) : ?>
				<div class="ec-sl__intro-text">
					<?php echo wp_kses_post( wpautop( $intro_text ) ); ?>
				</div>
				<?php endif; ?>

				<p class="ec-sl__contact">
					<a href="mailto:<?php echo esc_attr( $intro_email ); ?>">
						<?php echo esc_html( $label_contact . ' ' . $intro_email ); ?>
					</a>
				</p>
				<div class="ec-sl__actions">
					<a href="<?php echo esc_url( $intro_booking ); ?>" class="ec-sl__btn ec-sl__btn--primary">
						<?php echo esc_html( $label_book ); ?>
					</a>
				</div>
			</div>
		</div>

		<?php foreach ( $experiences as $experience ) :
			$thumb_large = get_the_post_thumbnail_url( $experience->getId(), 'large' );
			$full_desc   = $experience->getFullDescription() ?: $experience->getShortDescription();
		?>
		<!-- Slide: <?php echo esc_html( $experience->getTitle() ); ?> -->
		<div class="ec-sl__slide" id="<?php echo esc_attr( $block_uid . '-exp-' . $experience->getId() ); ?>">
			<div class="ec-sl__slide-inner">

				<?php if ( $thumb_large ) : ?>
				<figure class="ec-sl__slide-image">
					<img src="<?php echo esc_url( $thumb_large ); ?>"
						alt="<?php echo esc_attr( $experience->getTitle() ); ?>" />
				</figure>
				<?php endif; ?>

				<div class="ec-sl__slide-body">
					<h2 class="ec-sl__slide-title"><?php echo esc_html( $experience->getTitle() ); ?></h2>

					<div class="ec-sl__slide-meta">
						<?php if ( $experience->getDuration() ) : ?>
						<span><?php printf( esc_html__( '%d min', 'experience-crud' ), $experience->getDuration() ); ?></span>
						<?php endif; ?>
						<span>
							<?php printf(
								esc_html__( 'Groups: %1$d-%2$d', 'experience-crud' ),
								$experience->getMinPersons(),
								$experience->getMaxPersons()
							); ?>
						</span>
					</div>

					<?php if ( $full_desc ) : ?>
					<div class="ec-sl__slide-desc">
						<?php echo wp_kses_post( wpautop( $full_desc ) ); ?>
					</div>
					<?php endif; ?>

					<?php if ( ! empty( $experience->getIncludes() ) ) : ?>
					<div class="ec-sl__detail-section">
						<h4><?php esc_html_e( 'Includes', 'experience-crud' ); ?></h4>
						<ul>
							<?php foreach ( $experience->getIncludes() as $item ) : ?>
							<li>
								<?php if ( ! empty( $item['url'] ) ) : ?>
									<a href="<?php echo esc_url( $item['url'] ); ?>" target="_blank" rel="noopener">
										<?php echo esc_html( $item['text'] ); ?>
									</a>
								<?php else : ?>
									<?php echo esc_html( $item['text'] ); ?>
								<?php endif; ?>
							</li>
							<?php endforeach; ?>
						</ul>
					</div>
					<?php endif; ?>

					<?php if ( ! empty( $experience->getTastings() ) ) : ?>
					<div class="ec-sl__detail-section">
						<h4><?php esc_html_e( 'Tastings', 'experience-crud' ); ?></h4>
						<ul>
							<?php foreach ( $experience->getTastings() as $item ) : ?>
							<li>
								<?php if ( ! empty( $item['url'] ) ) : ?>
									<a href="<?php echo esc_url( $item['url'] ); ?>" target="_blank" rel="noopener">
										<?php echo esc_html( $item['text'] ); ?>
									</a>
								<?php else : ?>
									<?php echo esc_html( $item['text'] ); ?>
								<?php endif; ?>
							</li>
							<?php endforeach; ?>
						</ul>
					</div>
					<?php endif; ?>

					<?php if ( ! empty( $experience->getRequirements() ) ) : ?>
					<div class="ec-sl__detail-section">
						<h4><?php esc_html_e( 'Considerations', 'experience-crud' ); ?></h4>
						<ul>
							<?php foreach ( $experience->getRequirements() as $req ) : ?>
							<li><?php echo esc_html( $req ); ?></li>
							<?php endforeach; ?>
						</ul>
					</div>
					<?php endif; ?>

					<p class="ec-sl__contact">
						<a href="mailto:<?php echo esc_attr( $experience->getEmail() ); ?>">
							<?php echo esc_html( $label_contact . ' ' . $experience->getEmail() ); ?>
						</a>
					</p>

					<div class="ec-sl__actions">
						<a href="<?php echo esc_url( $experience->getBookingUrl() ); ?>"
							class="ec-sl__btn ec-sl__btn--primary">
							<?php echo esc_html( $label_book ); ?>
						</a>
						<button class="ec-sl__btn ec-sl__btn--back"
							data-uid="<?php echo esc_attr( $block_uid ); ?>"
							data-target="<?php echo esc_attr( $block_uid . '-intro' ); ?>">
							<?php echo esc_html( $label_back ); ?>
						</button>
					</div>
				</div>

			</div>
		</div>
		<?php endforeach; ?>

	</div><!-- /.ec-sl__slider -->
</div><!-- /.ec-sl -->

<script>
( function () {
	var uid = <?php echo wp_json_encode( $block_uid ); ?>;
	var root = document.getElementById( uid );
	if ( ! root ) return;

	function showSlide( slideId ) {
		root.querySelectorAll( '.ec-sl__slide' ).forEach( function ( s ) {
			s.classList.remove( 'ec-sl__slide--active' );
		} );
		root.querySelectorAll( '.ec-sl__thumb' ).forEach( function ( t ) {
			t.classList.remove( 'ec-sl__thumb--active' );
		} );
		var target = document.getElementById( slideId );
		if ( target ) target.classList.add( 'ec-sl__slide--active' );
	}

	root.querySelectorAll( '.ec-sl__thumb' ).forEach( function ( thumb ) {
		thumb.addEventListener( 'click', function () {
			if ( thumb.dataset.uid !== uid ) return;
			thumb.classList.add( 'ec-sl__thumb--active' );
			showSlide( thumb.dataset.target );
		} );
	} );

	root.querySelectorAll( '.ec-sl__btn--back' ).forEach( function ( btn ) {
		btn.addEventListener( 'click', function () {
			showSlide( btn.dataset.target );
		} );
	} );
} )();
</script>
