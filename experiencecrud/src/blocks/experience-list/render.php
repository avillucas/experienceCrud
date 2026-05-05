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

// Preparamos datos para Schema.org
$schema_data = [
    "@context" => "https://schema.org",
    "@type" => "ItemList",
    "itemListElement" => []
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
        <!-- Slide 0: Our Experience (fijo al inicio) -->
        <div class="slide active" data-slide-index="0">
            <div class="slide-container slide-container--centered">
                <figure class="slide-image">
                    <img src="http://localhost:8088/wp-content/uploads/2025/05/nuestras-experiencias.svg" alt="Nuestras Experiencias" style="width: 500px; margin-top: 50px;" />
                </figure>
                <div class="slide-content">
                    <p>Si está planeando un viaje a Mendoza - ARG, nos encantaría que nos visite en La Pirámide, nuestra bodega familiar.</p>
                    <p>Nuestros guías especializados realizan tours para grupos reducidos, por lo que recomendamos reservar un día y horario con anticipación.</p>
                    <p>Por favor haga clic en el siguiente enlace para realizar su reserva. ¡Lo esperamos!</p>
                    
                    <div class="contact-link-wrapper">
                        <a href="mailto:turismo@catenazapata.com" style="font-size: 14px; font-weight: 400; line-height: 1.7; color: #000 !important;">
                            Contactanos: turismo@catenazapata.com
                        </a>
                    </div>

                    <div class="slide-buttons">
                        <div class="wp-block-button">
                            <a class="wp-block-button__link" 
                               href="https://catenazapata.meitre.com/"
                               style="background-color: #000; color: #fff; border-radius: 0; padding: 7px 12px; font-size: 12px; font-weight: 300; letter-spacing: 1px; text-transform: uppercase; text-decoration: none;">
                                RESERVAR
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php 
        $count = 1;
        $query = new WP_Query( $args ); // Reset query for the slides
        if ( $query->have_posts() ) :
            while ( $query->have_posts() ) : $query->the_post(); 
                $id = get_the_ID();
                $title = get_the_title();
                $summary = get_post_meta( $id, 'ec_summary', true );
                $duration = get_post_meta( $id, 'ec_duration_min', true );
                $min_members = get_post_meta( $id, 'ec_min_members', true );
                $max_members = get_post_meta( $id, 'ec_max_members', true );
                $price = get_post_meta( $id, 'ec_price', true );
                $price_from = get_post_meta( $id, 'ec_price_valid_from', true );
                $price_to = get_post_meta( $id, 'ec_price_valid_to', true );
                $booking_url = get_post_meta( $id, 'ec_booking_url', true );
                $contact_email = get_post_meta( $id, 'ec_contact_email', true );
                $related_raw = get_post_meta( $id, 'ec_related_products', true );
                $legacy_prices = get_post_meta( $id, 'ec_prices_list', true );

                // Agregar a Schema
                $schema_item = [
                    "@type" => "ListItem",
                    "position" => $count,
                    "item" => [
                        "@type" => "Service",
                        "name" => $title,
                        "description" => $summary ?: wp_trim_words( get_the_content(), 20 ),
                        "provider" => [
                            "@type" => "Winery",
                            "name" => "Catena Zapata"
                        ],
                        "url" => get_permalink()
                    ]
                ];
                if ( $price ) {
                    $schema_item['item']['offers'] = [
                        "@type" => "Offer",
                        "price" => $price,
                        "priceCurrency" => "ARS",
                        "url" => $booking_url ?: get_permalink()
                    ];
                    if ( $price_to ) {
                        $schema_item['item']['offers']['priceValidUntil'] = $price_to;
                    }
                }
                $schema_data['itemListElement'][] = $schema_item;
                ?>
                <div class="slide" data-slide-index="<?php echo esc_attr( $count ); ?>">
                    <div class="slide-container">
                        <figure class="slide-image">
                            <?php if ( has_post_thumbnail() ) : ?>
                                <?php the_post_thumbnail( 'large', ['style' => 'width: 400px; display: block; margin: 0 auto;'] ); ?>
                            <?php endif; ?>
                        </figure>
                        
                        <div class="slide-content slide-content--left">
                            <?php if ( $summary ) : ?>
                                <p class="experience-summary"><strong><?php echo esc_html( $summary ); ?></strong></p>
                            <?php endif; ?>

                            <div class="experience-description">
                                <?php the_content(); ?>
                            </div>

                            <?php if ( $duration || $min_members || $max_members ) : ?>
                                <p><strong>Detalles:</strong></p>
                                <ul>
                                    <?php if ( $duration ) : ?>
                                        <li>Duración: <?php echo esc_html( $duration ); ?> min</li>
                                    <?php endif; ?>
                                    <?php if ( $min_members || $max_members ) : ?>
                                        <li>Capacidad: 
                                            <?php 
                                            if ( $min_members && $max_members ) {
                                                printf( 'Grupos de %1$s a %2$s personas', esc_html( $min_members ), esc_html( $max_members ) );
                                            } elseif ( $max_members ) {
                                                printf( 'Hasta %s personas', esc_html( $max_members ) );
                                            } else {
                                                printf( 'Mínimo %s personas', esc_html( $min_members ) );
                                            }
                                            ?>
                                        </li>
                                    <?php endif; ?>
                                </ul>
                            <?php endif; ?>

                            <?php if ( $price || $legacy_prices ) : ?>
                                <p><strong>Precio:</strong></p>
                                <div class="prices-content">
                                    <?php if ( $price ) : ?>
                                        <p>AR$ <?php echo number_format( $price, 0, ',', '.' ); ?> p/p 
                                        <?php if ( $price_from || $price_to ) : ?>
                                            desde <?php echo esc_html( $price_from ?: '...' ); ?> hasta <?php echo esc_html( $price_to ?: '...' ); ?>
                                        <?php endif; ?>
                                        </p>
                                    <?php endif; ?>
                                    <?php if ( $legacy_prices ) echo wpautop( esc_html( $legacy_prices ) ); ?>
                                </div>
                            <?php endif; ?>

                            <?php if ( $related_raw ) : ?>
                                <p><strong>Productos Relacionados:</strong></p>
                                <ul class="related-products">
                                    <?php 
                                    $lines = explode( "\n", $related_raw );
                                    foreach ( $lines as $line ) {
                                        $parts = explode( "|", $line );
                                        if ( count( $parts ) >= 1 ) {
                                            $name = trim( $parts[0] );
                                            $url = isset( $parts[1] ) ? trim( $parts[1] ) : '#';
                                            echo '<li><a href="' . esc_url( $url ) . '">' . esc_html( $name ) . '</a></li>';
                                        }
                                    }
                                    ?>
                                </ul>
                            <?php endif; ?>

                            <?php if ( $contact_email ) : ?>
                                <div class="contact-link-wrapper">
                                    <a href="mailto:<?php echo esc_attr( $contact_email ); ?>" style="font-size: 14px; font-weight: 400; line-height: 1.7; color: #000 !important;">
                                        Contactanos: <?php echo esc_html( $contact_email ); ?>
                                    </a>
                                </div>
                            <?php endif; ?>

                            <div class="slide-buttons">
                                <?php if ( $booking_url ) : ?>
                                    <div class="wp-block-button">
                                        <a class="wp-block-button__link" 
                                           href="<?php echo esc_url( $booking_url ); ?>"
                                           style="background-color: #000; color: #fff; border-radius: 0; padding: 7px 12px; font-size: 12px; font-weight: 300; letter-spacing: 1px; text-transform: uppercase; text-decoration: none;">
                                            RESERVAR
                                        </a>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <div class="slide-buttons">
                                <button class="go-back-button">VOLVER</button>
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

<!-- Schema.org Data -->
<script type="application/ld+json">
<?php echo json_encode( $schema_data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT ); ?>
</script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const slideBoxes = document.querySelectorAll('.slide-box');
    const slides = document.querySelectorAll('.slide');
    const goBackButtons = document.querySelectorAll('.go-back-button');

    slideBoxes.forEach(box => {
        box.addEventListener('click', function () {
            const slideIndex = parseInt(this.getAttribute('data-slide'));

            slides.forEach(slide => {
                slide.classList.remove('active');
                slide.style.display = 'none';
            });
            slideBoxes.forEach(b => b.classList.remove('active'));

            if (slides[slideIndex]) {
                slides[slideIndex].style.display = 'block';
                setTimeout(() => {
                    slides[slideIndex].classList.add('active');
                }, 10);
            }
            this.classList.add('active');
        });
    });

    goBackButtons.forEach(button => {
        button.addEventListener('click', function () {
            slides.forEach(slide => {
                slide.classList.remove('active');
                slide.style.display = 'none';
            });
            slideBoxes.forEach(b => b.classList.remove('active'));

            slides[0].style.display = 'block';
            setTimeout(() => {
                slides[0].classList.add('active');
            }, 10);
        });
    });
});
</script>
<?php
return ob_get_clean();
