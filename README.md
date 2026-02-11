# Jengo Base

The core package for Jengo applications, providing essential utilities, installers, and structure for CodeIgniter 4 rapid development.

## Features

- **Rapid Setup**: Command-line installers for common stacks.
- **Frontend Integration**: Seamless support for modern frontend tools like Vite.
- **Helpers & Utilities**: Specialized helpers for Jengo's architecture.

## Installers

Jengo Base includes several installers to jumpstart your development.

### [Vite Installer](docs/installers/vite.md)

Sets up a complete Vite environment with optional Tailwind CSS support.

```bash
php spark jengo:install vite
```

## Installation

Install the package via Composer:

```bash
composer require jengo/base
```

## Usage

Register the package in your CodeIgniter 4 application and use the provided `spark` commands.

```bash
php spark list
```

Look for the `jengo` namespace to see available commands.