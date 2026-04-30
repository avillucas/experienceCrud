<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class EC_CPT {

	public function register() {
		$labels = [
			'name'                  => _x( 'Experiencias', 'Post Type General Name', 'experience-crud' ),
			'singular_name'         => _x( 'Experiencia', 'Post Type Singular Name', 'experience-crud' ),
			'menu_name'             => __( 'Experiencias', 'experience-crud' ),
			'name_admin_bar'        => __( 'Experiencia', 'experience-crud' ),
			'archives'              => __( 'Archivo de Experiencias', 'experience-crud' ),
			'attributes'            => __( 'Atributos de Experiencia', 'experience-crud' ),
			'parent_item_colon'     => __( 'Experiencia Padre:', 'experience-crud' ),
			'all_items'             => __( 'Todas las Experiencias', 'experience-crud' ),
			'add_new_item'          => __( 'Añadir Nueva Experiencia', 'experience-crud' ),
			'add_new'               => __( 'Añadir Nueva', 'experience-crud' ),
			'new_item'              => __( 'Nueva Experiencia', 'experience-crud' ),
			'edit_item'             => __( 'Editar Experiencia', 'experience-crud' ),
			'update_item'           => __( 'Actualizar Experiencia', 'experience-crud' ),
			'view_item'             => __( 'Ver Experiencia', 'experience-crud' ),
			'view_items'            => __( 'Ver Experiencias', 'experience-crud' ),
			'search_items'          => __( 'Buscar Experiencia', 'experience-crud' ),
			'not_found'             => __( 'No se encontraron experiencias', 'experience-crud' ),
			'not_found_in_trash'    => __( 'No se encontraron experiencias en la papelera', 'experience-crud' ),
			'featured_image'        => __( 'Imagen Destacada', 'experience-crud' ),
			'set_featured_image'    => __( 'Asignar imagen destacada', 'experience-crud' ),
			'remove_featured_image' => __( 'Quitar imagen destacada', 'experience-crud' ),
			'use_featured_image'    => __( 'Usar como imagen destacada', 'experience-crud' ),
			'insert_into_item'      => __( 'Insertar en la experiencia', 'experience-crud' ),
			'uploaded_to_this_item' => __( 'Subido a esta experiencia', 'experience-crud' ),
			'items_list'            => __( 'Lista de experiencias', 'experience-crud' ),
			'items_list_navigation' => __( 'Navegación de lista de experiencias', 'experience-crud' ),
			'filter_items_list'     => __( 'Filtrar lista de experiencias', 'experience-crud' ),
		];

		$args = [
			'label'               => __( 'Experiencia', 'experience-crud' ),
			'description'         => __( 'Post Type para Experiencias Vitivinícolas', 'experience-crud' ),
			'labels'              => $labels,
			'supports'            => [ 'title', 'editor', 'thumbnail', 'excerpt', 'custom-fields' ],
			'hierarchical'        => false,
			'public'              => true,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'menu_position'       => 5,
			'menu_icon'           => 'dashicons-palmtree',
			'show_in_admin_bar'   => true,
			'show_in_nav_menus'   => true,
			'can_export'          => true,
			'has_archive'         => true,
			'exclude_from_search' => false,
			'publicly_queryable'  => true,
			'show_in_rest'        => true, // Habilitar Gutenberg
			'capability_type'     => 'post',
		];

		register_post_type( 'experiencia', $args );
	}
}
