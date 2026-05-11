# Experience CRUD – CLAUDE.md

## Resumen del proyecto

Plugin WordPress para **Catena Zapata** que gestiona experiencias vitivinícolas (CPT `experiencia`).
Arquitectura hexagonal (DDD): dominio en `src/Core/Domain/`, infraestructura WordPress en `src/Infrastructure/WordPress/`.
Polylang gestiona el multiidioma.

---

## Entorno de desarrollo

| Componente | Detalles |
|---|---|
| WordPress | `wordpress:latest` en Docker, puerto 8088 |
| DB | MariaDB 10.6, contenedor `catenab_db` |
| Node builder | `node:18`, contenedor `catenab_node` |
| Plugin en WP | `dist/` montado en `/var/www/html/wp-content/plugins/experiencecrud` |

### Comandos útiles

```bash
# Rebuild completo (desde el host, donde esté Node)
npm run build

# Copiar solo PHP/templates (sin compilar JS)
npm run copy-php

# Copiar solo CSS de bloques a dist/
npm run copy-css

# Reiniciar el node-builder (aplica cambios a webpack.config.js o experience-crud.php)
docker restart catenab_node

# Ver logs en tiempo real
docker logs -f catenab_node
docker logs -f catenab_wp
```

### Flujo de archivos

```
src/                         →  npm run build / wp-scripts start  →  dist/
├── experience-crud.php      →  copy-php                          →  dist/experience-crud.php
├── Core/, Infrastructure/,
│   includes/, templates/    →  copy-php                          →  dist/src/, dist/templates/
├── blocks/*/index.js        →  webpack                           →  dist/blocks/*/index.js
├── blocks/*/block.json      →  CopyWebpackPlugin                 →  dist/blocks/*/block.json
├── blocks/*/render.php      →  CopyWebpackPlugin                 →  dist/blocks/*/render.php
└── blocks/*/*.css           →  CopyWebpackPlugin (regla custom)  →  dist/blocks/*/*.css
```

**Nota crítica**: `@wordpress/scripts` por defecto solo copia `block.json` y `.php`. Los archivos CSS
de los bloques se copian mediante una regla extra en `webpack.config.js` (agregada en esta sesión).
Sin esa regla, los CSS no llegan a `dist/` y WordPress puede generar warnings al registrar los bloques.

---

## Estructura del plugin

```
src/
├── Core/Domain/
│   ├── Experience.php              # Entidad del dominio
│   └── ExperienceRepository.php   # Interfaz del repositorio
├── Infrastructure/WordPress/
│   ├── WordPressExperienceRepository.php  # Implementación con WP_Query
│   └── MetaHandler.php            # Registro CPT + post_meta
├── includes/
│   ├── class-ec-cpt.php
│   ├── class-ec-i18n.php
│   ├── class-ec-meta.php
│   └── class-ec-polylang.php
├── blocks/
│   ├── experience-header-slider/  # Hero/slider con imagen de fondo editable
│   ├── experience-list/           # Grid de cards + modales (render_callback)
│   └── experience-slider-list/    # Selector de thumbnails + panel de detalle (render_callback)
├── templates/
│   └── page-experiencias.php      # Plantilla de página personalizada
├── sidebar/index.js               # Sidebar de Gutenberg
└── experience-crud.php            # Archivo principal del plugin
```

### Autoload Composer (PSR-4)

```
ExperienceCrud\ → src/
```
En producción (Docker): `dist/vendor/autoload.php` apunta a `dist/src/`.
La clase `WordPressExperienceRepository` debe estar en `dist/src/Infrastructure/WordPress/`.

---

## Bloques Gutenberg

### Regla para bloques con ServerSideRender

**Siempre usar `render_callback` en PHP** para bloques que usen `ServerSideRender` en el editor.
No usar `"render": "file:./render.php"` en `block.json` para este caso.

Patrón correcto en `block.json`:
```json
{
  "editorScript": "file:./index.js"
}
```

Patrón correcto en `render.php`:
```php
<?php
// ... lógica ...
ob_start();
?>
<!-- HTML del bloque -->
<?php
return ob_get_clean();
```

Registro en `experience-crud.php`:
```php
register_block_type(
    EC_PATH . 'blocks/mi-bloque',
    [ 'render_callback' => 'ec_render_mi_bloque' ]
);

function ec_render_mi_bloque( $attributes ) {
    return require EC_PATH . 'blocks/mi-bloque/render.php';
}
```

### Regla para estilos de bloques

**No declarar `"style"` ni `"editorStyle"` en `block.json`** si los archivos CSS no están garantizados
en `dist/`. WordPress llama a `realpath()` al registrar el bloque; si el archivo no existe,
en PHP 8.1+ puede generar un warning que contamina la respuesta REST y produce el error
`"La respuesta no es una respuesta JSON válida"`.

Alternativas válidas:
- Usar la regla CopyWebpackPlugin del `webpack.config.js` (ya configurada) y reiniciar el node-builder
- Registrar los estilos manualmente desde `experience-crud.php` con `wp_enqueue_block_style()`

### Bloques existentes

| Bloque | Render | ServerSideRender | CSS en block.json |
|---|---|---|---|
| `ec/experience-header-slider` | `"render": "file:./render.php"` | No (React directo) | Sí (OK, archivos existen) |
| `ec/experience-list` | `render_callback` | Sí | No (encolado por PHP) |
| `ec/experience-slider-list` | `render_callback` | Sí | No (pendiente, ver abajo) |

---

## Bloque `ec/experience-slider-list`

Diseño implementado: replica el diseño de `htmlactual.html`.

- **Selector de thumbnails** (`.ec-sl__selector`): franja vertical con imágenes destacadas de cada experiencia del CPT. Al clickear muestra el panel de detalle correspondiente.
- **Panel introductorio** (`.ec-sl__slide--active` por defecto): configurable desde el Inspector de Gutenberg (logo/SVG, texto, URL de reservas, email).
- **Paneles de experiencia** (uno por CPT publicado): título, duración, grupos, descripción, incluye, degustaciones, consideraciones, botón reservar, botón volver.
- **JavaScript inline** con IDs únicos (`wp_unique_id()`) para soportar múltiples instancias en la misma página.

### Atributos editables (Inspector de Gutenberg)
| Atributo | Tipo | Descripción |
|---|---|---|
| `introLogoUrl` | string | URL del SVG/imagen del panel intro |
| `introText` | string | Texto descriptivo del panel intro |
| `introBookingUrl` | string | URL del botón BOOK NOW en el intro |
| `introEmail` | string | Email de contacto del intro |

### Estado actual de CSS
Los archivos `style.css` y `editor.css` existen en `src/blocks/experience-slider-list/`
pero **aún no están en `dist/`**. Para activar los estilos:

```bash
docker restart catenab_node
```

Esto correrá `npm run build` con el `webpack.config.js` actualizado que incluye la regla
para copiar CSS. Una vez que los archivos estén en `dist/`, agregar a `block.json`:
```json
"style": "file:./style.css",
"editorStyle": "file:./editor.css"
```

---

## Reglas para modificar este plugin

1. **Cambios a JS/CSS de bloques** → guardados en `src/`, el node-builder los compila automáticamente.
2. **Cambios a `render.php` de bloques** → guardados en `src/`, el node-builder los copia automáticamente.
3. **Cambios a `experience-crud.php`** → guardados en `src/`, requieren `docker restart catenab_node`.
4. **Cambios a `webpack.config.js`** → requieren `docker restart catenab_node`.
5. **Agregar un nuevo bloque** → crear archivos en `src/blocks/<nombre>/`, agregar entry en `webpack.config.js`, registrar en `src/experience-crud.php`. Luego reiniciar el node-builder.
6. **CSS de bloques** → se copian con la regla CopyWebpackPlugin en `webpack.config.js`. Siempre reiniciar el node-builder tras agregar un nuevo bloque con CSS.

---

## Errores conocidos y soluciones

### "La respuesta no es una respuesta JSON válida" en Gutenberg

**Causa más frecuente**: `block.json` referencia un archivo CSS que no existe en `dist/`.
WordPress al registrar el bloque llama `realpath()` sobre el path del CSS; si no existe, en PHP 8.1+
puede generar un warning que se imprime antes del JSON de la REST API.

**Solución**:
- Eliminar `"style"` y `"editorStyle"` de `block.json` hasta que los archivos existan en dist/
- O reiniciar el node-builder para que webpack copie los CSS

**Causa secundaria**: usar `"render": "file:./render.php"` con `ServerSideRender`. Usar siempre
`render_callback` para bloques que necesitan ServerSideRender.

### CSS no aparece en `dist/blocks/`

**Causa**: `CopyWebpackPlugin` de `@wordpress/scripts` solo copia `block.json` y `.php` por defecto.

**Solución**: La regla custom en `webpack.config.js` (ya implementada) copia todos los `style.css`
y `editor.css` de `src/blocks/`. Requiere reiniciar el node-builder para que tome efecto.
