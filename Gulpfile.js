var gulp = require('gulp');
var chug = require('gulp-chug');
var sass = require('gulp-sass');
var autoprefixer = require('gulp-autoprefixer');
var argv = require('yargs').argv;
// var svgSprite = require('gulp-svg-sprite');

config = [
    '--rootPath',
    argv.rootPath || '../../../../../../../web/assets/',
    '--nodeModulesPath',
    argv.nodeModulesPath || '../../../../../../../node_modules/'
];

gulp.task('admin', function () {
    gulp.src('vendor/sylius/sylius/src/Sylius/Bundle/AdminBundle/Gulpfile.js', {
            read: false
        })
        .pipe(chug({
            args: config
        }));
});

gulp.task('shop', function () {
    gulp.src('vendor/sylius/sylius/src/Sylius/Bundle/ShopBundle/Gulpfile.js', {
            read: false
        })
        .pipe(chug({
            args: config
        }));

});

gulp.task('sass', function () {
    gulp.src('./web/sass/**/*.scss')
        .pipe(sass().on('error', sass.logError))
        .pipe(autoprefixer({
            browsers: ['last 2 versions'],
            cascade: false
        }))
        .pipe(gulp.dest('./web/assets/shop/css'));
});

gulp.task('adminSass', function () {
    gulp.src('./web/assets/admin/sass/**/*.scss')
        .pipe(sass().on('error', sass.logError))
        .pipe(autoprefixer({
            browsers: ['last 2 versions'],
            cascade: false
        }))
        .pipe(gulp.dest('./web/assets/admin/css'));
});



gulp.task('sass:watch', function () {
    gulp.watch('./web/sass/**/*.scss', ['sass']);
});

gulp.task('adminSass:watch', function () {
    gulp.watch('./web/assets/admin/sass/**/*.scss', ['adminSass']);
});


gulp.task('default', ['admin', 'shop']);