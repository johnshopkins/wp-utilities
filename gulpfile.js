var gulp = require("gulp");
var phpunit = require("gulp-phpunit");
var git = require("gulp-git");
var argv = require("minimist")(process.argv.slice(2));


/**
 * Tasks
 * 
 */

gulp.task("phpunit", function() {
    gulp.src("./tests/**/*Test.php").pipe(phpunit());
});

gulp.task("release", function () {

});



/**
 * Watch
 * 
 */

gulp.task("watch", function () {
  gulp.watch(["./src/**/*.php", "./tests/**/*.php"], ["phpunit"]);
});



/**
 * Build
 * 
 */

// gulp.task("default", ["compile:js", "compile:css", "move:images", "move:htaccess"]);
