=== Experience CRUD ===
Contributors: avillucas
Tags: winery, experiences, gutenberg, polylang
Requires at least: 6.0
Tested up to: 6.5
Stable tag: 1.0.0
License: GPLv2 or later

Gestión de Experiencias vitivinícolas de lujo para Bodega Catena Zapata.

== Description ==

Este plugin permite gestionar "Experiencias" como un Post Type personalizado con campos específicos (duración, degustaciones, inclusiones, capacidad, etc.). Incluye bloques de Gutenberg premium para mostrar un Slider de cabecera y un listado interactivo con detalles en modal.

== Installation ==

1. Sube la carpeta `experiencecrud` al directorio `/wp-content/plugins/`.
2. Activa el plugin a través del menú 'Plugins' en WordPress.
3. Asegúrate de tener Polylang instalado si deseas soporte multi-idioma.

== Usage ==

1. Ve a "Experiencias" en el menú lateral.
2. Crea una nueva experiencia y completa los campos en la barra lateral derecha (Experience Details).
3. En cualquier página, añade el bloque "Experience Header Slider" para la cabecera.
4. Añade el bloque "Experience List" para mostrar el listado de experiencias.

== Architecture ==

El plugin utiliza Arquitectura Hexagonal y DDD:
- Core/Domain: Entidades y Repositorios.
- Infrastructure: Implementación específica de WordPress.
