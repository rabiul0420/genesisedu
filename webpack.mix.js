let mix = require('laravel-mix');

mix.webpackConfig({
   stats: {
      children: true,
   },
});

mix.js("vue/src/main.js", "public/vue")
   .vue();

mix.postCss("resources/css/tailwind.css", "public/tailwind/app.css"); // tailwind

mix.postCss("resources/css/client.css", "public/tailwind/client.css"); // vue

mix.options({
   postCss: [
      require("tailwindcss")
   ]
});