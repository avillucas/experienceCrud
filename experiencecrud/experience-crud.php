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

/**
 * Autoload de clases (Simple)
 */
spl_autoload_register( function ( $class ) {
	$prefix = 'EC_';
	if ( strpos( $class, $prefix ) !== 0 ) {
		return;
	}

	$file = EC_PATH . 'includes/class-' . strtolower( str_replace( '_', '-', $class ) ) . '.php';
	if ( file_exists( $file ) ) {
		require_once $file;
	}
} );

/**
 * Inicializar el plugin
 */
function ec_init_plugin() {
	// Registro de CPT y Meta
	$cpt = new EC_CPT();
	$meta = new EC_Meta();
	$metaboxes = new EC_Metaboxes();
	$i18n = new EC_I18n();

	add_action( 'init', [ $cpt, 'register' ] );
	add_action( 'init', [ $meta, 'register' ] );
	add_action( 'add_meta_boxes', [ $metaboxes, 'register' ] );
	add_action( 'save_post', [ $metaboxes, 'save' ] );
	add_action( 'plugins_loaded', [ $i18n, 'load_textdomain' ] );
}
add_action( 'plugins_loaded', 'ec_init_plugin' );

/**
 * Registro de bloques de Gutenberg
 */
function ec_register_blocks() {
	register_block_type( 'ec/experience-list', [
		'render_callback' => 'ec_render_experience_list',
		'editor_script'   => 'ec-blocks-js',
		'style'           => 'ec-blocks-css',
	] );
}
add_action( 'init', 'ec_register_blocks' );

/**
 * Render callback para el bloque
 */
function ec_render_experience_list( $attributes ) {
	return require EC_PATH . 'src/blocks/experience-list/render.php';
}

/**
 * Encolar scripts y estilos
 */
function ec_enqueue_assets() {
	$asset_file = include( EC_PATH . 'dist/index.asset.php' );

	wp_enqueue_script(
		'ec-blocks-js',
		EC_URL . 'dist/index.js',
		$asset_file['dependencies'],
		$asset_file['version']
	);

	wp_enqueue_style(
		'ec-blocks-css',
		EC_URL . 'src/blocks/experience-list/style.css',
		[],
		'1.0.0'
	);
}
add_action( 'enqueue_block_editor_assets', 'ec_enqueue_assets' );
add_action( 'wp_enqueue_scripts', 'ec_enqueue_assets' );
