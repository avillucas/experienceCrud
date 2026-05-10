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

        if ( ! empty( $lang ) ) {
            $args['lang'] = $lang;
        } elseif ( function_exists( 'pll_current_language' ) ) {
            $args['lang'] = pll_current_language();
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
            $this->unserializeMeta( $meta, 'ec_includes', [] ),
            $this->unserializeMeta( $meta, 'ec_tastings', [] ),
            (int) ( $meta['ec_min_persons'][0] ?? 1 ),
            (int) ( $meta['ec_max_persons'][0] ?? 10 ),
            $this->unserializeMeta( $meta, 'ec_requirements', [] ),
            $meta['ec_contact_email'][0] ?? 'turismo@catenazapata.com',
            $meta['ec_booking_url'][0] ?? 'https://catenazapata.meitre.com/',
            (int) ( $meta['ec_duration'][0] ?? 60 )
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
        update_post_meta( $id, 'ec_includes', wp_json_encode( $experience->getIncludes() ) );
        update_post_meta( $id, 'ec_tastings', wp_json_encode( $experience->getTastings() ) );
        update_post_meta( $id, 'ec_min_persons', $experience->getMinPersons() );
        update_post_meta( $id, 'ec_max_persons', $experience->getMaxPersons() );
        update_post_meta( $id, 'ec_requirements', wp_json_encode( $experience->getRequirements() ) );
        update_post_meta( $id, 'ec_contact_email', $experience->getEmail() );
        update_post_meta( $id, 'ec_booking_url', $experience->getBookingUrl() );
        update_post_meta( $id, 'ec_duration', $experience->getDuration() );
    }

    private function unserializeMeta( $meta, $key, $default ) {
        if ( ! isset( $meta[ $key ][0] ) ) {
            return $default;
        }
        $value = json_decode( $meta[ $key ][0], true );
        return is_array( $value ) ? $value : $default;
    }
}
