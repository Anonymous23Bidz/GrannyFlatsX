# Hello Elementor Child theme assets

Production only needs the PHP files and the committed CSS in `assets/css/`.
It does **not** need Node.js, npm, `node_modules`, or a Sass compiler.

## Local CSS workflow

Install the development dependency once on your local machine:

```sh
npm install
```

Compile the Sass entry point once:

```sh
npm run build:css
```

Or keep it compiling while you edit:

```sh
npm run watch:css
```

Edit the Sass partials in `assets/scss/pages/` and `assets/scss/components/`,
or edit `assets/scss/main.scss` to change the stylesheet entry order or add a
new partial. Commit the resulting `assets/css/main.css` with your source
changes.

`assets/css/main.css` is the only CSS file enqueued by WordPress; it contains
the compiled styles and has no runtime Sass or npm dependency.
