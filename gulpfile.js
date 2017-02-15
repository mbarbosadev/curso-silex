const gulp = require('gulp');
const brouserSync = require('browser-sync').create();
const less = require('gulp-less');

let DIR = {
	less: './assets/less',
	build: './public/build'
}

DIR.build_css = `${DIR.build}/css`;

gulp.task('browser-sync', function() {
	brouserSync.init({
		proxy: "localhost:8000",
		files: [
			"src/**/**.php",
			"public/index.php",
			"templates/**/**.phtml",
			`${DIR.build_css}/**/*.css`
		]
	});
});

gulp.task('less', function() {
	gulp.src(`${DIR.less}/style.less`)
		.pipe(less())
		.pipe(gulp.dest(DIR.build_css));
});

gulp.task('watch', function() {
	gulp.watch([`${DIR.less}/**/*.less`], ['less']);
});

gulp.task('dev', ['watch', 'less', 'browser-sync']);