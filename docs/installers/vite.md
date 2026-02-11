# Vite Installer

The Vite installer sets up a modern frontend development environment using [Vite](https://vitejs.dev/) and optional [Tailwind CSS](https://tailwindcss.com/) integration.

## Installation

Run the following command to install Vite and configure your project:

```bash
php spark jengo:install vite
```

## Features

- **Automated Setup**: Installs Vite and configures `vite.config.js`.
- **Tailwind CSS Support**: Optionally installs and configures Tailwind CSS.
- **Dynamic Entrypoints**: Automatically discovers `*.entrypoint.ts`, `*.entrypoint.js`, `*.entrypoint.css`, and `*.entrypoint.scss` files in your `app/` directory.
- **ESM Support**: Configures your project for ECMAScript Modules (ESM).

## Configuration

### `vite.config.ts`

The installer creates a `vite.config.ts` file at your project root. It uses the `@jengo/vite` plugin to handle CodeIgniter 4 integration.

```typescript
import { defineConfig } from 'vite';
import jengo from '@jengo/vite';
// import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        jengo(),
        // tailwindcss(), // If Tailwind is enabled
    ],
});
```

Ensure to uncomment the `tailwindcss` plugin if you have Tailwind CSS installed.

### `package.json`

Your `package.json` will be updated with the necessary dependencies:

- `vite`
- `@jengo/vite`
- `tailwindcss` & `@tailwindcss/vite` (optional)

Ensure your `package.json` includes `"type": "module"` to downstream tools that the project uses ESM.

## Usage

Start the development server:

```bash
npm run dev
```

Build for production:

```bash
npm run build
```

This will compile your assets to `public/dist`.
