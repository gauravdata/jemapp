// Required plugins
var gulp = require('gulp');
var plumber = require('gulp-plumber');
var sass = require('gulp-sass');
var minifyCSS = require('gulp-minify-css');
var uglify = require('gulp-uglify');
var concat = require('gulp-concat');

var paths = {
    scss: {
        src: ['skin/frontend/twm/lemarais/scss/**/*.scss'],
		includes: ['node_modules/simple-line-icons/scss/'],
        dest: 'skin/frontend/twm/lemarais/dist/css/'
    }
};

gulp.task('styles', function () {
    gulp.src(paths.scss.src)
        .pipe(plumber())
        .pipe(sass({includePaths: paths.scss.includes}))
        .pipe(minifyCSS({compatibility: 'ie8'}))
        .pipe(gulp.dest(paths.scss.dest));
});

gulp.task('watch', function() {
    gulp.watch(paths.scss.src, ['styles']);
});

gulp.task('build', ['styles']);
gulp.task('default', ['build', 'watch']);
