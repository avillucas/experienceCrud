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

## Compilación (Docker & Composer)

Este plugin utiliza `@wordpress/scripts` para compilar los recursos de Gutenberg. Si utilizas el entorno de Docker incluido, puedes compilar los assets sin tener Node instalado localmente:

**Usando Docker Compose directamente:**
```bash
docker compose run --rm node-builder npm run build
```

**Usando Composer (si está configurado en tu flujo):**
```bash
composer build
```

*(Nota: El contenedor `node-builder` en `docker-compose.yml` está configurado para ejecutar `npm run start` por defecto, lo que vigila los cambios en tiempo real).*

## Uso

1. Ve a **Experiencias > Añadir nueva**.
2. Completa el título, descripción (editor principal) e imagen destacada.
3. En la barra lateral derecha (Panel de Datos de la Experiencia), completa los datos técnicos:
   - Resumen (Obligatorio para SEO y Slider).
   - Precio y validez.
   - Duración, capacidad, etc.
4. En cualquier página, añade el bloque **Experience List**.

## Créditos
Inspirado en el diseño de Bodega Catena Zapata.
