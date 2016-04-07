// Required plugins
var gulp = require('gulp');
var plumber = require('gulp-plumber');
var sass = require('gulp-sass');
var minifyCSS = require('gulp-minify-css');
var uglify = require('gulp-uglify');
var sourcemaps = require('gulp-sourcemaps');
var concat = require('gulp-concat');

var paths = {
    scss: {
        src: ['skin/frontend/twm/default/scss/**/*.scss'],
		includes: ['node_modules/mdi/scss/'],
        dest: 'skin/frontend/twm/default/dist/css/'
    }
};

gulp.task('styles', function () {
    gulp.src(paths.scss.src)
        .pipe(plumber())
        .pipe(sourcemaps.init())
        .pipe(sass({includePaths: paths.scss.includes}))
        .pipe(minifyCSS({compatibility: 'ie8'}))
        .pipe(sourcemaps.write())
        .pipe(gulp.dest(paths.scss.dest));
});

gulp.task('watch', function() {
    gulp.watch(paths.scss.src, ['styles']);
});

gulp.task('build', ['styles']);
gulp.task('default', ['build', 'watch']);
