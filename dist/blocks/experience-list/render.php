<?php
/**
 * Render callback for the ec/experience-list block.
 * Layout: alternating image/text rows following Figma design.
 */

use ExperienceCrud\Infrastructure\WordPress\WordPressExperienceRepository;

$repository = new WordPressExperienceRepository();

// Detect the current language: Polylang frontend → REST ?lang param → repository fallback.
$lang = '';
if ( function_exists( 'pll_current_language' ) ) {
	$lang = pll_current_language() ?: '';
}
if ( empty( $lang ) && isset( $_GET['lang'] ) ) {
	$lang = sanitize_text_field( wp_unslash( $_GET['lang'] ) );
}

$experiences = $repository->findAll( $lang );

$schema_data = [
	'@context'        => 'https://schema.org',
	'@type'           => 'ItemList',
	'itemListElement' => [],
];

ob_start();
?>
<div class="ec-exp-list">
	<?php foreach ( $experiences as $index => $experience ) :
		$thumbnail_url = get_the_post_thumbnail_url( $experience->getId(), 'large' );
		$is_image_right = $index % 2 !== 0;
		$schema_data['itemListElement'][] = [
			'@type'    => 'ListItem',
			'position' => $index + 1,
			'item'     => [
				'@type'       => 'Service',
				'name'        => $experience->getTitle(),
				'description' => $experience->getShortDescription(),
				'url'         => get_permalink( $experience->getId() ),
			],
		];
	?>
		<div class="ec-exp-row <?php echo $is_image_right ? 'ec-exp-row--image-right' : 'ec-exp-row--image-left'; ?>">
			<div class="ec-exp-row__image">
				<?php if ( $thumbnail_url ) : ?>
					<img
						src="<?php echo esc_url( $thumbnail_url ); ?>"
						alt="<?php echo esc_attr( $experience->getTitle() ); ?>"
						loading="lazy"
					/>
				<?php endif; ?>
			</div>

			<div class="ec-exp-row__content">
				<h2 class="ec-exp-row__title"><?php echo esc_html( $experience->getTitle() ); ?></h2>
				<?php if ( $experience->getShortDescription() ) : ?>
					<p class="ec-exp-row__desc"><?php echo esc_html( $experience->getShortDescription() ); ?></p>
				<?php endif; ?>
				<button
					class="ec-exp-row__btn open-modal"
					data-target="modal-<?php echo esc_attr( $experience->getId() ); ?>"
				>
					<?php echo esc_html( ec_t( 'MORE INFORMATION', 'MÁS INFORMACIÓN' ) ); ?>
				</button>
			</div>
		</div>

		<dialog id="modal-<?php echo esc_attr( $experience->getId() ); ?>" class="experience-modal">
			<div class="modal-content">
				<button class="close-modal" aria-label="<?php echo esc_attr( ec_t( 'Close', 'Cerrar' ) ); ?>">&times;</button>
				<div class="modal-body">
					<div class="modal-header">
						<h2 class="modal-title"><?php echo esc_html( $experience->getTitle() ); ?></h2>
					</div>

					<div class="modal-description">
						<?php echo wp_kses_post( do_blocks( $experience->getFullDescription() ?: $experience->getShortDescription() ) ); ?>
					</div>

					<div class="modal-footer">
						<a href="<?php echo esc_url( $experience->getBookingUrl() ); ?>" class="btn-booking" target="_blank" rel="noopener">
							<?php echo esc_html( ec_t( 'BOOK NOW', 'RESERVAR' ) ); ?>
						</a>
						<p class="contact-info">
							<span><?php echo esc_html( ec_t( 'Contact us:', 'Contáctenos:' ) ); ?></span>
							<a href="mailto:<?php echo esc_attr( $experience->getEmail() ); ?>"><?php echo esc_html( $experience->getEmail() ); ?></a>
						</p>
					</div>
				</div>
			</div>
		</dialog>
	<?php endforeach; ?>
</div>

<script type="application/ld+json">
<?php echo wp_json_encode( $schema_data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT ); ?>
</script>

<script>
(function () {
	function initModals() {
		document.querySelectorAll('.open-modal').forEach(function (btn) {
			btn.addEventListener('click', function () {
				var modal = document.getElementById(btn.dataset.target);
				if (modal) modal.showModal();
			});
		});
		document.querySelectorAll('.close-modal').forEach(function (btn) {
			btn.addEventListener('click', function () {
				btn.closest('dialog').close();
			});
		});
		document.querySelectorAll('.experience-modal').forEach(function (modal) {
			modal.addEventListener('click', function (e) {
				if (e.target === modal) modal.close();
			});
		});
	}
	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', initModals);
	} else {
		initModals();
	}
})();
</script>
<?php
return ob_get_clean();
