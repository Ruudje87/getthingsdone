var gulp = require('gulp');
var watch = require('gulp-watch');
var sass = require('gulp-sass');
var concat = require('gulp-concat');
var minify = require('gulp-minify');
var cleanCss = require('gulp-clean-css');
var source = require('vinyl-source-stream');
var request = require('request');
var merge = require('merge2');
var buffer = require('gulp-buffer');
var rename = require('gulp-rename');
var hash = require('gulp-hash');
var clean = require('gulp-clean');


var basepath = "public";

gulp.task('sass', function () {
    gulp.src([basepath+'/css/local/sass/import.scss'])
        .pipe(sass({outputStyle: 'compressed'}))
        .pipe(rename('sass.css'))
        .pipe(gulp.dest(basepath+'/css/local'));
});

gulp.task('clean-css', function () {
  return gulp.src(basepath+'/css/final/*.css', {read: false})
    .pipe(clean());
});

gulp.task('pack-css', ['clean-css'], function () {	
	return gulp.src([basepath+'/css/vendor/**/*.css',basepath+'/css/vendor/*.css',basepath+'/css/local/**/*.css',basepath+'/css/local/*.css'])
		.pipe(concat('style.css'))
                .pipe(cleanCss())
		.pipe(gulp.dest(basepath+'/css'))
                .pipe(hash())
                .pipe(gulp.dest(basepath+'/css/final'));
});
gulp.task('clean-js', function () {
  return gulp.src(basepath+'/js/final/*.js', {read: false})
    .pipe(clean());
});

gulp.task('pack-js', ['clean-js'], function () {
    
    return gulp.src([basepath+'/js/vendor/**/*.js',basepath+'/js/vendor/*.js',basepath+'/js/local/**/*.js',basepath+'/js/local/*.js'])
		.pipe(concat('script.js'))
                .pipe(minify({
                    ext: {
                        min: '.js'
                    },
                    noSource: true
                }))
		.pipe(gulp.dest(basepath+'/js'))
                .pipe(hash())
                .pipe(gulp.dest(basepath+'/js/final'));
});




// Default task
gulp.task('default', ['sass','pack-css','pack-js']);