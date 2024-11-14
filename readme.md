# 7g

A lightweight hybrid WordPress starter theme.

- ACF block components (custom HTML-Element)
- JS Islands. Load js only if needed. (visible, on load, media query...)
- Hook PHP attribute for simple add_filter assignment
- ACF fields registered in PHP -> [Extend ACF](https://github.com/vinkla/extended-acf)
- Vite + Composer
- Dynamic resize of images
- Block Areas CPT to show components on not editable sites like archives...

**Some concepts and code (partially modified) from [Flynt Theme](https://github.com/flyntwp/flynt)**

## Requirements
- [ACF PRO Plugin](https://www.advancedcustomfields.com/pro/)
- PHP 8.0+

## Getting Started

Run the following commands from the theme root to install Composer and NPM packages:
```bash
composer install
npm install
```

Edit the .env.example file and rename it to .env

To start the Vite dev server, run:
```bash
npm run dev
```

To build the assets, run:
```bash
npm run build
```

## License
MIT