<?php
/**
 * Render callback for the ec/experience-list block.
 */

use ExperienceCrud\Infrastructure\WordPress\WordPressExperienceRepository;

$repository = new WordPressExperienceRepository();
$experiences = $repository->findAll();

$schema_data = [
	'@context'        => 'https://schema.org',
	'@type'           => 'ItemList',
	'itemListElement' => [],
];

ob_start();
?>
<div class="ec-experience-list-wrapper"> 
	<div class="experience-grid">
		<?php foreach ( $experiences as $index => $experience ) : 
			$thumbnail_url = get_the_post_thumbnail_url( $experience->getId(), 'large' );
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
			<div class="experience-card">
				<div class="experience-card__image" style="background-image: url('<?php echo esc_url( $thumbnail_url ); ?>');"></div>
				<div class="experience-card__content">
					<h3 class="experience-card__title"><?php echo esc_html( $experience->getTitle() ); ?></h3>
					<p class="experience-card__excerpt"><?php echo esc_html( $experience->getShortDescription() ); ?></p>
					<button class="experience-card__button open-modal" data-target="modal-<?php echo $experience->getId(); ?>">
						<?php echo esc_html( function_exists( 'pll__' ) ? pll__( 'MORE INFORMATION' ) : __( 'MORE INFORMATION', 'experience-crud' ) ); ?>
					</button>
				</div>
			</div>

			<!-- Modal for details -->
			<dialog id="modal-<?php echo $experience->getId(); ?>" class="experience-modal">
				<div class="modal-content">
					<button class="close-modal">&times;</button>
					<div class="modal-body">
						<div class="modal-header">
							<h2 class="modal-title"><?php echo esc_html( $experience->getTitle() ); ?></h2>
							<div class="modal-meta">
								<span><?php printf( esc_html__( '%d min', 'experience-crud' ), $experience->getDuration() ); ?></span>
								<span><?php printf( esc_html__( 'Groups: %d-%d', 'experience-crud' ), $experience->getMinPersons(), $experience->getMaxPersons() ); ?></span>
							</div>
						</div>
						
						<div class="modal-description">
							<?php echo wp_kses_post( wpautop( $experience->getFullDescription() ?: $experience->getShortDescription() ) ); ?>
						</div>

						<div class="modal-details-grid">
							<?php if ( ! empty( $experience->getIncludes() ) ) : ?>
								<div class="detail-section">
									<h4><?php esc_html_e( 'Includes', 'experience-crud' ); ?></h4>
									<ul>
										<?php foreach ( $experience->getIncludes() as $item ) : ?>
											<li>
												<?php if ( ! empty( $item['url'] ) ) : ?>
													<a href="<?php echo esc_url( $item['url'] ); ?>" target="_blank"><?php echo esc_html( $item['text'] ); ?></a>
												<?php else : ?>
													<?php echo esc_html( $item['text'] ); ?>
												<?php endif; ?>
											</li>
										<?php endforeach; ?>
									</ul>
								</div>
							<?php endif; ?>

							<?php if ( ! empty( $experience->getTastings() ) ) : ?>
								<div class="detail-section">
									<h4><?php esc_html_e( 'Tastings', 'experience-crud' ); ?></h4>
									<ul>
										<?php foreach ( $experience->getTastings() as $item ) : ?>
											<li>
												<?php if ( ! empty( $item['url'] ) ) : ?>
													<a href="<?php echo esc_url( $item['url'] ); ?>" target="_blank"><?php echo esc_html( $item['text'] ); ?></a>
												<?php else : ?>
													<?php echo esc_html( $item['text'] ); ?>
												<?php endif; ?>
											</li>
										<?php endforeach; ?>
									</ul>
								</div>
							<?php endif; ?>
						</div>

						<div class="modal-footer">
							<a href="<?php echo esc_url( $experience->getBookingUrl() ); ?>" class="btn-booking">
								<?php echo esc_html( function_exists( 'pll__' ) ? pll__( 'BOOK NOW' ) : __( 'BOOK NOW', 'experience-crud' ) ); ?>
							</a>
							<p class="contact-info">
								<?php esc_html_e( 'Questions?', 'experience-crud' ); ?> 
								<a href="mailto:<?php echo esc_attr( $experience->getEmail() ); ?>"><?php echo esc_html( $experience->getEmail() ); ?></a>
							</p>
						</div>
					</div>
				</div>
			</dialog>
		<?php endforeach; ?>
	</div>
</div>

<script type="application/ld+json">
<?php echo wp_json_encode( $schema_data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT ); ?>
</script>

<script>
document.addEventListener('DOMContentLoaded', () => {
	const openButtons = document.querySelectorAll('.open-modal');
	const closeButtons = document.querySelectorAll('.close-modal');

	openButtons.forEach(btn => {
		btn.addEventListener('click', () => {
			const modal = document.getElementById(btn.dataset.target);
			if (modal) modal.showModal();
		});
	});

	closeButtons.forEach(btn => {
		btn.addEventListener('click', () => {
			btn.closest('dialog').close();
		});
	});

	// Close on click outside
	document.querySelectorAll('.experience-modal').forEach(modal => {
		modal.addEventListener('click', (e) => {
			if (e.target === modal) modal.close();
		});
	});
});
</script>
<?php
return ob_get_clean();
