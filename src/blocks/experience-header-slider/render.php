<?php
/**
 * Render callback for the ec/experience-header-slider block.
 */

$title = $attributes['title'] ?? '';
$description = $attributes['description'] ?? '';

// Translatable strings if Polylang is active
if ( function_exists( 'pll__' ) ) {
    $title = pll__( $title );
    $description = pll__( $description );
}
?>
<div class="ec-header-slider">
    <div class="ec-header-slider__overlay"></div>
    <div class="ec-header-slider__container">
        <div class="ec-header-slider__content">
            <h1 class="ec-header-slider__title"><?php echo wp_kses_post( $title ); ?></h1>
            <div class="ec-header-slider__divider"></div>
            <p class="ec-header-slider__description"><?php echo wp_kses_post( $description ); ?></p>
        </div>
    </div>
</div>
