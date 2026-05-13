<?php

namespace ExperienceCrud\Infrastructure\WordPress;

class MetaHandler {
    public function register() {
        $this->register_cpt();
        $this->register_meta();
    }

    private function register_cpt() {
        $labels = [
            'name'                  => _x( 'Experiencias', 'Post Type General Name', 'experience-crud' ),
            'singular_name'         => _x( 'Experiencia', 'Post Type Singular Name', 'experience-crud' ),
            'menu_name'             => __( 'Experiencias', 'experience-crud' ),
        ];

        $args = [
            'label'               => __( 'Experiencia', 'experience-crud' ),
            'labels'              => $labels,
            'supports'            => [ 'title', 'editor', 'thumbnail', 'excerpt', 'custom-fields' ],
            'public'              => true,
            'show_in_rest'        => true,
            'menu_icon'           => 'dashicons-palmtree',
            'has_archive'         => false,
            'rewrite'             => [ 'slug' => 'experiencia' ],
        ];

        register_post_type( 'experiencia', $args );
    }

    private function register_meta() {
        $fields = [
            'ec_contact_email' => [ 'type' => 'string', 'default' => 'turismo@catenazapata.com' ],
            'ec_booking_url'   => [ 'type' => 'string', 'default' => 'https://catenazapata.meitre.com/' ],
        ];

        foreach ( $fields as $key => $args ) {
            register_post_meta( 'experiencia', $key, [
                'show_in_rest' => true,
                'single'       => true,
                'type'         => $args['type'],
                'default'      => $args['default'],
                'auth_callback' => function() {
                    return current_user_can( 'edit_posts' );
                }
            ] );
        }
    }
}
