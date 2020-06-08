var gulp = require('gulp'),
  cache = require('gulp-cache'),
  cleancss = require('gulp-clean-css'),
  tiny = require('gulp-tinypng-compress'),
  concat = require('gulp-concat'),
  uglify = require('gulp-uglify'),
  pump = require('pump');

var paths = {
  images: {
    src: 'resource/images/*.*',
    dest: 'src/assets/images'
  },
  js: {
    src: [
    'resource/vendor/zepto.min.js',
    'resource/vendor/fastclick.js',
    'resource/vendor/preloadjs.min.js',
    'resource/vendor/EasePack.min.js',
    'resource/vendor/BezierPlugin.min.js',
    'resource/vendor/TweenMax.min.js'
    ],
    dest: 'share-order-game/vendor'
  },
  css: {
    src: 'resource/css/*.css',
    dest: 'resource/css/dest'
  }
}
var date_str = getDateTimeString();
// declare tasks
gulp.task(js);
gulp.task(css);
gulp.task(images);
gulp.task(watch);

gulp.task('default', images);

gulp.task('compress', function (cb) {
  pump([
        gulp.src('resource/vendor/zepto.js'),
        uglify(),
        gulp.dest('resource/vendor/zepto.min.js')
    ],
    cb
  );
});
//合并js
function js() {
  return gulp.src(paths.js.src)
        .pipe(concat('vendor.'+date_str+'.min.js'))
        .pipe(gulp.dest(paths.js.dest));
}
function css() {
  return gulp.src([paths.css.src])
        .pipe(cleancss())
        .pipe(gulp.dest(paths.css.dest));
}
//压缩图片
function images() {
  return gulp.src([paths.images.src])
        .pipe(tiny({
            key: 'A8MrhgFiA4z4SNXeGRbqdnqsaYZCOCtC',
            sigFile: 'resource/images/.tinypng-sigs',
            log: true
        }))
        .pipe(gulp.dest(paths.images.dest));
}
//watch  执行 gulp watch 可以监听
function watch() {
  gulp.watch(paths.images.src, images);
  // gulp.watch(paths.js.src, vendorjs);
}

function getDateTimeString(){
    var date = new Date();
    var year_str = date.getFullYear().toString();
    var month_str = (date.getMonth()+1)>9?String(date.getMonth()+1):"0"+(date.getMonth()+1);
    var date_str = date.getDate()>9?date.getDate().toString():"0"+date.getDate();
    var hour_str = date.getHours()>9?date.getHours().toString():"0"+date.getHours();
    var min_str = date.getMinutes()>9?date.getMinutes().toString():"0"+date.getMinutes();
    return year_str+month_str+date_str+hour_str+min_str;
}

