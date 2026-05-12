<?php

namespace ExperienceCrud\Infrastructure\WordPress;

use ExperienceCrud\Core\Domain\Experience;
use ExperienceCrud\Core\Domain\ExperienceRepository;
use WP_Query;

class WordPressExperienceRepository implements ExperienceRepository {
    public function findAll(string $lang = ''): array {
        $args = [
            'post_type'      => 'experiencia',
            'posts_per_page' => -1,
            'post_status'    => 'publish',
        ];

        $resolved_lang = $lang;

        if ( empty( $resolved_lang ) && function_exists( 'pll_current_language' ) ) {
            $resolved_lang = pll_current_language() ?: '';
        }

        if ( empty( $resolved_lang ) && function_exists( 'pll_get_post_language' ) ) {
            $queried = get_queried_object();
            if ( $queried instanceof \WP_Post ) {
                $resolved_lang = pll_get_post_language( $queried->ID ) ?: '';
            }
        }

        if ( ! empty( $resolved_lang ) ) {
            $args['lang'] = $resolved_lang;
        }

        $query = new WP_Query( $args );
        $experiences = [];

        if ( $query->have_posts() ) {
            while ( $query->have_posts() ) {
                $query->the_post();
                $experiences[] = $this->findById( get_the_ID() );
            }
            wp_reset_postdata();
        }

        return $experiences;
    }

    public function findById($id): ?Experience {
        $post = get_post( $id );
        if ( ! $post || $post->post_type !== 'experiencia' ) {
            return null;
        }

        $meta = get_post_meta( $id );

        return new Experience(
            $id,
            $post->post_title,
            $post->post_excerpt,
            $post->post_content,
            (int) get_post_thumbnail_id( $id ),
            $meta['ec_contact_email'][0] ?? 'turismo@catenazapata.com',
            $meta['ec_booking_url'][0] ?? 'https://catenazapata.meitre.com/'
        );
    }

    public function save(Experience $experience): void {
        // Implementation for saving (used in REST or Sidebar)
        $data = [
            'ID'           => $experience->getId(),
            'post_title'   => $experience->getTitle(),
            'post_excerpt' => $experience->getShortDescription(),
            'post_content' => $experience->getFullDescription(),
            'post_type'    => 'experiencia',
        ];

        if ( $experience->getId() ) {
            wp_update_post( $data );
        } else {
            $id = wp_insert_post( $data );
            // Update ID if needed
        }

        $id = $experience->getId();
        update_post_meta( $id, 'ec_contact_email', $experience->getEmail() );
        update_post_meta( $id, 'ec_booking_url', $experience->getBookingUrl() );
    }


}
