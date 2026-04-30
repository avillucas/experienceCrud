# Experience CRUD (Catena Zapata Style)

Plugin de WordPress para la gestión de Experiencias vitivinícolas de lujo. Permite crear un listado elegante de experiencias con detalles extendidos en una ventana modal, diseñado bajo la identidad visual de Bodega Catena Zapata.

## Características

- **Custom Post Type**: `experiencia`.
- **Gutenberg Sidebar**: Edición de metadatos (duración, capacidad, precios) directamente en la barra lateral del editor.
- **Bloque Dinámico**: Bloque `ec/experience-list` para mostrar las experiencias en el frontend.
- **Diseño Premium**: Tipografía Serif, fondos crema y layout minimalista.
- **Accesibilidad**: Modal basado en la API nativa `<dialog>`.
- **Internacionalización**: Compatible con Polylang y preparado para traducción.

## Requisitos

- WordPress 6.0+
- PHP 7.4+
- Polylang (Recomendado para multi-idioma)

## Instalación y Compilación

Este plugin utiliza `@wordpress/scripts` para compilar los recursos de Gutenberg.

1. Sube la carpeta `experiencecrud` al directorio `/wp-content/plugins/`.
2. Abre una terminal en la carpeta del plugin.
3. Instala las dependencias:
   ```bash
   npm install
   ```
4. Compila el plugin para producción:
   ```bash
   npm run build
   ```
   O para desarrollo con auto-recarga:
   ```bash
   npm run start
   ```
5. Activa el plugin desde el panel de administración de WordPress.

## Uso

1. Ve a **Experiencias > Añadir nueva**.
2. Completa el título, descripción (editor principal) e imagen destacada.
3. En la barra lateral derecha (Panel de Experiencia), completa los datos técnicos:
   - Duración (minutos).
   - Capacidad mínima y máxima.
   - Lista de precios.
   - Email de contacto.
   - URL de reserva externa.
4. En cualquier página, añade el bloque **Experience List**.

## Créditos
Inspirado en el diseño de Bodega Catena Zapata.
