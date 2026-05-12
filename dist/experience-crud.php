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
} else {
	spl_autoload_register( function ( $class ) {
		$prefix   = 'ExperienceCrud\\';
		$base_dir = EC_PATH . 'src/';
		if ( strncmp( $prefix, $class, strlen( $prefix ) ) !== 0 ) {
			return;
		}
		$file = $base_dir . str_replace( '\\', '/', substr( $class, strlen( $prefix ) ) ) . '.php';
		if ( file_exists( $file ) ) {
			require $file;
		}
	} );
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
}
add_action( 'init', 'ec_register_blocks' );

/**
 * Render callback para el bloque
 */
function ec_render_experience_list( $attributes ) {
	return require EC_PATH . 'blocks/experience-list/render.php';
}

/**
 * Returns a string in the current page language (ES or EN).
 * Uses same language-detection fallback chain as the repository.
 */
function ec_t( string $en, string $es ): string {
	$lang = '';
	if ( function_exists( 'pll_current_language' ) ) {
		$lang = pll_current_language() ?: '';
	}
	if ( empty( $lang ) && function_exists( 'pll_get_post_language' ) ) {
		$queried = get_queried_object();
		if ( $queried instanceof \WP_Post ) {
			$lang = pll_get_post_language( $queried->ID ) ?: '';
		}
	}
	return $lang === 'es' ? $es : $en;
}

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
