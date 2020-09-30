const gulp = require('gulp');
const cleanCSS = require('gulp-clean-css');
const sourcemaps = require('gulp-sourcemaps');

const {parallel} = require('gulp');

function copyJavaScriptLibraries() {
    return gulp.src(
        [
            'node_modules/tablesort/dist/tablesort.min.js',
            'node_modules/tablesort/dist/sorts/tablesort.number.min.js'
        ]
    ).pipe(gulp.dest('js'));
}

function minify() {
    return gulp.src('css/style.css')
        .pipe(sourcemaps.init())
        .pipe(cleanCSS())
        .pipe(sourcemaps.write('./', {
            mapFile: true
        }))
        .pipe(gulp.dest('css/dist'));
}

exports.default = parallel(copyJavaScriptLibraries, minify);
exports.build = minify;
