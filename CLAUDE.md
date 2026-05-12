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
| Plugin en WP | `dist/` montado como **bind mount** en `/var/www/html/wp-content/plugins/experiencecrud` |

> **Bind mount**: lo que está en el `dist/` del host ES exactamente lo que ve WordPress.
> Los archivos PHP no los genera webpack; deben copiarse con `copy-php`.

### Comandos útiles

```bash
# Reconstruir JS/CSS (desde el node-builder)
docker exec catenab_node sh -c "cd /usr/src/app && npm run build"

# Copiar PHP a dist/ (experience-crud.php + src/ + vendor/)
docker exec catenab_node sh -c "cd /usr/src/app && npm run copy-php"

# Reiniciar el node-builder (requerido al cambiar webpack.config.js)
docker restart catenab_node

# Después de reiniciar, restaurar los PHP (webpack borra dist/ en build limpio)
docker exec catenab_node sh -c "cd /usr/src/app && npm run copy-php"

# Ver logs en tiempo real
docker logs -f catenab_node
docker logs -f catenab_wp

# Limpiar cache de webpack (si hay errores por bloques eliminados)
docker exec catenab_node sh -c "rm -rf /usr/src/app/node_modules/.cache"
docker restart catenab_node
```

> ⚠️ **Tras `docker restart catenab_node`**: webpack hace un build limpio que reemplaza `dist/`.
> Los archivos PHP (`experience-crud.php`, `dist/src/`, `dist/vendor/`) no están en el output de
> webpack y deben restaurarse manualmente con `copy-php` después de cada restart.

### Flujo de archivos

```
src/                         →  webpack (watch)                    →  dist/
├── experience-crud.php      →  copy-php (manual)                  →  dist/experience-crud.php
├── Core/, Infrastructure/,
│   includes/                →  copy-php (manual)                  →  dist/src/
├── vendor/                  →  copy-php (manual)                  →  dist/vendor/
├── blocks/*/index.js        →  webpack compile                    →  dist/blocks/*/index.js
├── blocks/*/block.json      →  CopyWebpackPlugin (explícito)      →  dist/blocks/*/block.json
├── blocks/*/render.php      →  CopyWebpackPlugin (explícito)      →  dist/blocks/*/render.php
└── blocks/*/*.css           →  CopyWebpackPlugin (explícito)      →  dist/blocks/*/*.css
```

Todos los patrones de copia están declarados **explícitamente** en `webpack.config.js`.
No depender del comportamiento por defecto de `@wordpress/scripts` para archivos PHP/CSS.

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
│   └── experience-list/           # Layout alternante imagen/texto (render_callback)
├── sidebar/index.js               # Sidebar de Gutenberg (meta del CPT)
└── experience-crud.php            # Archivo principal del plugin
```

### Autoload Composer (PSR-4)

```
ExperienceCrud\ → src/
```
En producción (Docker): `dist/vendor/autoload.php` apunta a `dist/src/`.

---

## Bloque `ec/experience-list`

### Diseño

Layout alternante imagen/texto basado en el diseño Figma de la página de experiencias.

- **Fila impar** (0, 2, 4…): imagen izquierda — texto derecha (`.ec-exp-row--image-left`)
- **Fila par** (1, 3, 5…): texto izquierda — imagen derecha (`.ec-exp-row--image-right`)
- Cada fila: `min-height: 55vh`, imagen con `object-fit: cover`
- Botón "MORE INFORMATION" abre un `<dialog>` modal con detalle completo
- Fuentes: `Crimson Pro` / `Crimson Text` (serif) para títulos, `Montserrat` para cuerpo — las del tema, sin imports adicionales

### Registro

```php
// experience-crud.php
register_block_type(
    EC_PATH . 'blocks/experience-list',
    [ 'render_callback' => 'ec_render_experience_list' ]
);

function ec_render_experience_list( $attributes ) {
    return require EC_PATH . 'blocks/experience-list/render.php';
}
```

### block.json — solo editorScript

```json
{
  "editorScript": "file:./index.js"
}
```

**No declarar `"render"`, `"style"` ni `"editorStyle"`** en `block.json`.
- `"render"` conflicta con `render_callback` en bloques con `ServerSideRender`
- El CSS se encola manualmente desde `experience-crud.php` (`wp_enqueue_scripts`)

### render.php — patrón correcto

```php
ob_start();
?>
<!-- HTML -->
<?php
return ob_get_clean();
```

La función `require` en `ec_render_experience_list` captura el `return` como valor de retorno.
La descripción usa `do_blocks()` (no `wpautop()`) para procesar contenido Gutenberg correctamente.

### Meta del CPT

| Meta key | Tipo DB | Formato |
|---|---|---|
| `ec_includes` | string (JSON) | `[{"text":"...","url":"..."}]` |
| `ec_tastings` | string (JSON) | `[{"text":"...","url":"..."}]` |
| `ec_requirements` | string (JSON) | `[{"text":"..."}]` |
| `ec_duration` | integer | minutos |
| `ec_min_persons` | integer | — |
| `ec_max_persons` | integer | — |
| `ec_contact_email` | string | — |
| `ec_booking_url` | string | — |

**Compatibilidad retroactiva**: `unserializeMeta` en `WordPressExperienceRepository` normaliza
automáticamente items almacenados como strings planos al formato `{text, url}` actual.

---

## Bloques Gutenberg — Reglas

### ServerSideRender

**Siempre usar `render_callback` en PHP** para bloques con `ServerSideRender` en el editor.
Nunca usar `"render": "file:./render.php"` en `block.json` en ese caso — genera el error
`"La respuesta no es una respuesta JSON válida"` en Gutenberg.

### CSS en block.json

**No declarar `"style"` ni `"editorStyle"` en `block.json`** si los archivos no están garantizados
en `dist/`. WordPress llama a `realpath()` al registrar; si no existe, PHP 8.1+ genera un warning
que contamina la respuesta REST.

Alternativa válida: encolar con `wp_enqueue_scripts` / `enqueue_block_editor_assets` desde PHP.

---

## webpack.config.js

```js
plugins: [
    ...( defaultConfig.plugins || [] ),
    new CopyWebpackPlugin( {
        patterns: [
            { from: 'blocks/**/style.css',  context: 'src/', noErrorOnMissing: true },
            { from: 'blocks/**/editor.css', context: 'src/', noErrorOnMissing: true },
            { from: 'blocks/**/render.php', context: 'src/', noErrorOnMissing: true },
            { from: 'blocks/**/block.json', context: 'src/', noErrorOnMissing: true },
        ],
    } ),
],
```

Los cuatro tipos de archivo se declaran explícitamente. El `defaultConfig` de `@wordpress/scripts`
no es confiable para copiar PHP/CSS — puede fallar si hay bloques eliminados en cache.

---

## Errores conocidos y soluciones

### "La respuesta no es una respuesta JSON válida" en Gutenberg

| Causa | Solución |
|---|---|
| `block.json` tiene `"render"` + `ServerSideRender` | Eliminar `"render"` de `block.json`; usar solo `render_callback` en PHP |
| `block.json` referencia CSS inexistente en `dist/` | Eliminar `"style"`/`"editorStyle"` o asegurarse que los CSS estén en `dist/` |
| PHP warning antes de `ob_start()` en `render.php` | Revisar código pre-buffer; en REST, los warnings corrompen el JSON |

### "Failed opening required render.php"

**Causa**: webpack hizo un build limpio (tras restart o cambio de config) y eliminó `render.php`
de `dist/` porque no estaba en los patterns de CopyWebpackPlugin.

**Solución**: agregar `{ from: 'blocks/**/render.php', ... }` a CopyWebpackPlugin (ya hecho).
Si persiste, limpiar cache y reconstruir:

```bash
docker exec catenab_node sh -c "rm -rf /usr/src/app/node_modules/.cache"
docker restart catenab_node
# Esperar que webpack compile, luego:
docker exec catenab_node sh -c "cd /usr/src/app && npm run copy-php"
```

### Fatal error en render.php (PHP 8+): `Cannot access offset of type string on string`

**Causa**: meta `ec_includes` (u otro array) almacenado en formato antiguo como array de strings
planos `["texto1", "texto2"]` en vez de objetos `[{"text":"...","url":"..."}]`.

**Solución (ya aplicada)**: `unserializeMeta` en `WordPressExperienceRepository` normaliza
automáticamente strings a `['text' => $item, 'url' => '']`.

### Bloques eliminados rompen el build de webpack

**Causa**: webpack guarda en cache (`node_modules/.cache`) referencias a bloques que ya no existen
en `src/`. Al reconstruir, intenta resolver su `index.js` y falla.

**Solución**:
```bash
docker exec catenab_node sh -c "rm -rf /usr/src/app/node_modules/.cache"
docker restart catenab_node
```

### Contenido Gutenberg aparece con markup crudo (`<!-- wp:paragraph -->`)

**Causa**: usar `wpautop()` en lugar de `do_blocks()` para procesar `post_content`.

**Solución (ya aplicada)**: en `render.php`, usar:
```php
echo wp_kses_post( do_blocks( $experience->getFullDescription() ) );
```

---

## Página de experiencias (`contact-es`)

La página en `/contact-es/` (y su equivalente en español) usa bloques de Gutenberg:

1. **Hero**: `wp:cover` con imagen `piramide-2.jpg`, título "Tu experiencia en la piramide"
2. **Experiencias**: `<!-- wp:ec/experience-list /-->` — layout alternante del Figma
3. **Restaurante Angelica**: `wp:media-text` con imagen, logo, texto y botón reserva
4. **Tour Virtual**: `<iframe>` a `/tourvirtual/index.html`
5. **Dónde encontrarnos**: mapa Kadence + contacto + botón TripAdvisor
6. **Distribución**: textos y botones de distribuidores

No es un page template PHP: es contenido de página con bloques nativos de WordPress.
