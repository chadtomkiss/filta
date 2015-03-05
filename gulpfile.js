var gulp = require('gulp');
var sass = require('gulp-sass');
var watch = require('gulp-watch');
var autoprefix = require('gulp-autoprefixer');
var livereload = require('gulp-livereload');
var minifyCSS = require('gulp-minify-css');

// CSS concat, auto-prefix and minify
gulp.task('sass', function() {
	gulp.src(['./public/assets/sass/*.scss'])
		.pipe(sass())
		.pipe(autoprefix('last 2 versions'))
		.pipe(minifyCSS())
		.pipe(gulp.dest('./public/assets/css/'))
		.pipe(livereload());
});

// default gulp task
gulp.task('default', ['sass'], function() {
	// watch for CSS changes
	gulp.watch('./public/assets/sass/**/*.scss', ['sass']);
});