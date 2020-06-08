var gulp = require('gulp'),
  cache = require('gulp-cache'),
  imagemin = require('imagemin'),
  imgquant = require('imagemin-pngquant'),
  jpegtran = require('imagemin-jpegtran'),
  concat = require('gulp-concat');

var paths = {
  images: {
    src: 'resource/images/*.*',
    dest: 'src/components/assets/images'
  }
}
function images() {
  return cache(imagemin([paths.images.src], paths.images.dest, { use: [jpegtran(),imgquant()] }).then(() => {
    console.log('Images optimized');
  }));
}
//watch  执行 gulp watch 可以监听
function watch() {
  gulp.watch(paths.images.src, images);
}
//合并js
function concatjs() {
  //
}
// declare tasks
exports.images = images;
exports.concatjs = concatjs;
exports.watch = watch;

gulp.task('default', images);
