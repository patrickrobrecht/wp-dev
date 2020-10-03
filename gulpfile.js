const del = require('del');
const gulp = require('gulp');
const cleanCSS = require('gulp-clean-css');
const minify = require('gulp-minify');
const rename = require('gulp-rename');
const sourcemaps = require('gulp-sourcemaps');

const {parallel, series} = require('gulp');

function copyJavaScriptLibraries() {
    del('js/lib/*')
    return gulp.src(
        [
            'node_modules/tablesort/dist/tablesort.min.js',
            'node_modules/tablesort/dist/sorts/tablesort.number.min.js'
        ]
    ).pipe(gulp.dest('js/lib'));
}

function minifyCss() {
    del('css/*.min.*')
    return gulp.src('css/style.css')
        .pipe(sourcemaps.init())
        .pipe(cleanCSS())
        .pipe(rename({extname: '.min.css'}))
        .pipe(sourcemaps.write('./', {
            mapFile: true
        }))
        .pipe(gulp.dest('css'));
}

function minifyJavascript() {
    return gulp.src('js/functions.js')
        .pipe(minify({
            ext: {
                min: '.min.js'
            },
            noSource: true
        }))
        .pipe(gulp.dest('js'));
}

exports.default = parallel(copyJavaScriptLibraries, series(minifyCss, minifyJavascript));
exports.build = series(minifyCss, minifyJavascript);
