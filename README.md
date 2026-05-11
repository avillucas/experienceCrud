# Experience CRUD - Premium Winery Experiences

WordPress plugin for managing luxury winery experiences with a premium design, using Domain-Driven Design (DDD) and Hexagonal Architecture.

## 🚀 Getting Started

### Prerequisites
- Docker & Docker Compose
- Node.js & NPM (optional, can be run via Docker)
- Composer (optional, can be run via Docker)

### 1. Start the Development Environment
The project includes a `docker-compose.yml` that sets up WordPress, MariaDB, and a Node-based builder.

```bash
docker compose up -d
```
Access the site at: [http://localhost:8088](http://localhost:8088)

### 2. Install PHP Dependencies (Composer)
Run this command to install the Core and Infrastructure dependencies via Docker:

```bash
docker run --rm -v $(pwd):/app -w /app composer install
```

### 3. Compile Assets (Gutenberg Blocks)
To compile the JavaScript and CSS for the blocks and the Sidebar:

**Standard Build:**
```bash
docker compose run --rm node-builder npm run build
```

**Development Mode (Watch):**
```bash
docker compose run --rm node-builder npm run start
```

## 🏗 Architecture

The plugin is structured following **Hexagonal Architecture**:

- **`src/Core/Domain`**: Pure business logic and entities (`Experience`). No WordPress dependencies.
- **`src/Core/Application`**: Use cases and application services.
- **`src/Infrastructure/WordPress`**: WordPress-specific implementations (CPT registration, Meta, Repository).
- **`src/Infrastructure/Gutenberg`**: Block registration and Sidebar components.

## 📦 Distribution Structure

The build process synchronizes the source code into the `dist/` folder, which is the folder mapped as the plugin in WordPress:

- `src/` -> Compiled/Copied to `dist/`
- `vendor/` -> Copied to `dist/`
- `blocks/` -> Compiled to `dist/blocks/`

## 🧪 Testing

To run E2E tests (Playwright):
```bash
npm run test:e2e
```

## 🌐 Polylang Support

The plugin is fully compatible with Polylang. Ensure Polylang is active to enable multi-language experiences. The `ExperienceRepository` automatically filters experiences based on the current language.
