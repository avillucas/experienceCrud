<?php
/**
 * Plugin Name: Experience CRUD
 * Plugin URI: https://catenazapata.com
 * Description: Gestión de Experiencias vitivinícolas de lujo con integración en Gutenberg.
 * Version: 1.0.0
 * Author: Lucas
 * Text Domain: experience-crud
 * Domain Path: /languages
 * Requires at least: 6.0
 * Requires PHP: 7.4
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Definir constantes
define( 'EC_PATH', plugin_dir_path( __FILE__ ) );
define( 'EC_URL', plugin_dir_url( __FILE__ ) );

// Cargar Autoload de Composer
if ( file_exists( EC_PATH . 'vendor/autoload.php' ) ) {
	require_once EC_PATH . 'vendor/autoload.php';
}

/**
 * Inicializar el plugin
 */
function ec_init_plugin() {
	load_plugin_textdomain( 'experience-crud', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
}
add_action( 'plugins_loaded', 'ec_init_plugin' );

/**
 * Registrar el CPT en Polylang antes de que procese los post types
 */
add_filter( 'pll_get_post_types', function( $post_types ) {
	$post_types['experiencia'] = 'experiencia';
	return $post_types;
} );

/**
 * Registro de CPT y Meta (requiere init para que $wp_rewrite esté disponible)
 */
function ec_register_cpt_and_meta() {
	$meta_handler = new \ExperienceCrud\Infrastructure\WordPress\MetaHandler();
	$meta_handler->register();
}
add_action( 'init', 'ec_register_cpt_and_meta' );

/**
 * Registro de bloques de Gutenberg (auto-lee block.json del directorio)
 */
function ec_register_blocks() {
	register_block_type(
		EC_PATH . 'blocks/experience-list',
		[ 'render_callback' => 'ec_render_experience_list' ]
	);

	register_block_type( EC_PATH . 'blocks/experience-header-slider' );

	register_block_type(
		EC_PATH . 'blocks/experience-slider-list',
		[ 'render_callback' => 'ec_render_experience_slider_list' ]
	);
}
add_action( 'init', 'ec_register_blocks' );

/**
 * Render callback para el bloque
 */
function ec_render_experience_list( $attributes ) {
	return require EC_PATH . 'blocks/experience-list/render.php';
}

function ec_render_experience_slider_list( $attributes ) {
	return require EC_PATH . 'blocks/experience-slider-list/render.php';
}

/**
 * Registrar la plantilla de página "Experiencias".
 *
 * theme_page_templates  → la expone en el dropdown "Plantilla" del editor.
 * template_include      → la carga desde el plugin en lugar del tema.
 */
add_filter( 'theme_page_templates', function ( $templates ) {
	$templates['templates/page-experiencias.php'] = __( 'Experiencias', 'experience-crud' );
	return $templates;
} );

add_filter( 'template_include', function ( $template ) {
	if ( is_page() && get_page_template_slug() === 'templates/page-experiencias.php' ) {
		$plugin_template = EC_PATH . 'templates/page-experiencias.php';
		if ( file_exists( $plugin_template ) ) {
			return $plugin_template;
		}
	}
	return $template;
} );

/**
 * Encolar sidebar plugin en el editor
 */
function ec_enqueue_sidebar() {
	$asset_file = include( EC_PATH . 'index.asset.php' );
	wp_enqueue_script(
		'ec-sidebar-js',
		EC_URL . 'index.js',
		$asset_file['dependencies'],
		$asset_file['version']
	);
}
add_action( 'enqueue_block_editor_assets', 'ec_enqueue_sidebar' );

/**
 * Encolar estilo del bloque en el frontend
 */
function ec_enqueue_block_style() {
	wp_enqueue_style(
		'ec-blocks-css',
		EC_URL . 'blocks/experience-list/style.css',
		[],
		'1.0.0'
	);
}
add_action( 'wp_enqueue_scripts', 'ec_enqueue_block_style' );
