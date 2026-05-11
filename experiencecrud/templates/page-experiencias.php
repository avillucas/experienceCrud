<?php
/**
 * Template Name: Experiencias
 * Description: Página de experiencias vitivinícolas – header slider + selector de experiencias.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>
<main id="ec-page-experiencias" class="ec-page-experiencias">

	<?php
	/*
	 * Bloque: Header Slider
	 * El atributo backgroundImage puede configurarse por idioma a través del bloque
	 * en el editor. Aquí se usan los defaults declarados en block.json.
	 */
	echo do_blocks( '<!-- wp:ec/experience-header-slider /-->' );

	/*
	 * Bloque: Experience Slider List
	 * Muestra el selector de thumbnails + panel de detalle por experiencia.
	 * Los atributos introLogoUrl, introText, introBookingUrl e introEmail
	 * se gestionan en el editor por idioma.
	 */
	echo do_blocks( '<!-- wp:ec/experience-slider-list /-->' );
	?>

</main>
<?php
get_footer();
