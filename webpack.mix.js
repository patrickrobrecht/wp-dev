const mix = require('laravel-mix');

mix.sourceMaps(true, 'source-map');
mix.styles('css/style.css', 'css/style.min.css');
mix.js('js/functions.js', 'js/functions.min.js');

const javaScriptLibraries = [
    'node_modules/tablesort/dist/tablesort.min.js',
    'node_modules/tablesort/dist/sorts/tablesort.number.min.js'
];
javaScriptLibraries.forEach(file => mix.copy(file, 'js/lib'));
