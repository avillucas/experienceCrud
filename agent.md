Especificaciones del Proyecto: Experience CRUD (Catena Zapata Style)

Este documento sirve como guía técnica para la continuación del desarrollo del plugin Experience CRUD para WordPress.

1. Visión General

El plugin permite la gestión de "Experiencias" (Custom Post Type) orientadas al sector vitivinícola de lujo. La visualización se realiza mediante un bloque de Gutenberg que muestra un listado elegante y despliega detalles extendidos en una ventana modal.

2. Arquitectura Técnica Actual

Custom Post Type (CPT)

Slug: experiencia

Soportes: title, editor (para descripción larga), thumbnail (imagen principal), excerpt (resumen).

Compatibilidad: show_in_rest => true (Gutenberg habilitado) y soporte para Polylang.

Esquema de Metadatos (Post Meta)

Meta Key

Tipo

Descripción

ec_duration_min

Integer

Duración total expresada en minutos.

ec_min_members

Integer

Cantidad mínima de integrantes.

ec_max_members

Integer

Capacidad máxima de personas/grupo.

ec_prices_list

String

Lista de precios (guardado como texto multilínea).

ec_contact_email

String

Email para el enlace mailto:.

ec_booking_url

String

URL externa para el botón de reserva.

Bloque Gutenberg

Namespace: ec/experience-list

Tipo: Dinámico (ServerSideRender).

Comportamiento: Consulta todas las experiencias publicadas en el idioma actual del usuario.

3. Requisitos de Diseño (UI/UX)

El estilo debe ser Premium/Minimalista, basado en la identidad de Bodega Catena Zapata:

Tipografía: Preferencia por Serif (ej. 'EB Garamond' o similar).

Colores: Fondos crema suave (#fdfaf5), negros puros (#000000) para botones y grises sutiles.

Layout:

Listado: Imagen a la izquierda, texto a la derecha (en desktop).

Modal: Pantalla completa o caja centrada grande, tipografía de gran tamaño para títulos, y un botón de acción (Call to Action) prominente.


COmo guia para el diseño puedes usar las imagenes del figma que se encuentran en /home/lucas/Música/lucas/experienceCRUD/catena.png y /home/lucas/Música/lucas/experienceCRUD/catena-2.png. el linl al figma es https://www.figma.com/proto/3cUeE2bPLqPKuuUoBTDqc0/WEB---Experiencias?page-id=0%3A1&node-id=4308-242&viewport=-508%2C652%2C0.08&t=1Lb6Pwhtxy8oMWNq-8&scaling=scale-down-width&content-scaling=fixed&starting-point-node-id=4308%3A242&hide-ui=1

4. Tareas Pendientes / Backlog

Refactorización de Metadatos: Actualmente se usan metaboxes clásicos. Se recomienda migrar a campos nativos de Gutenberg (Sidebar) usando @wordpress/data y EntityProp para una experiencia de edición moderna.

Optimización del Modal: Implementar la API <dialog> nativa para mejorar la accesibilidad y el manejo del foco.

Internacionalización (i18n): Asegurar que todos los strings de la interfaz (ej: "Groups of up to", "Duration") pasen por funciones __() y sean compatibles con archivos .pot.

Galería de Imágenes: Evaluar la posibilidad de añadir un campo para galería de imágenes extra dentro del modal.

Validación de Datos: Añadir sanitización estricta en el guardado de la ec_booking_url y el ec_contact_email.

5. Notas de Polylang

El código actual detecta pll_current_language(). Es vital que cualquier nueva consulta (WP_Query) mantenga el parámetro 'lang' para no mezclar contenidos de distintos idiomas en el frontend.