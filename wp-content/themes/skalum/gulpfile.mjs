import gulp from "gulp";
import * as dartSass from "sass";
import gulpSass from "gulp-sass";
import postcss from "gulp-postcss";
import autoprefixer from "autoprefixer";
import cssnano from "cssnano";
import terser from "gulp-terser";
import rename from "gulp-rename";
import plumber from "gulp-plumber";
import path from "path";

const sass = gulpSass(dartSass);

// ---- Шляхи
const paths = {
  scssGlobal: "assets/scss/**/*.scss",
  cssGlobalOut: "assets/css",
  scssBlocks: "blocks/*/assets/scss/*.scss",
  jsBlocks: ["blocks/*/assets/js/*.js", "!blocks/*/assets/js/*.min.js"]
};

export function stylesGlobal() {
  return gulp.src(paths.scssGlobal, { sourcemaps: false })
    .pipe(plumber())
    .pipe(sass.sync({ outputStyle: "expanded" }))
    .pipe(postcss([autoprefixer(), cssnano()]))
    .pipe(gulp.dest(paths.cssGlobalOut));
}

export function stylesBlocks() {
  return gulp.src(paths.scssBlocks, { sourcemaps: false })
    .pipe(plumber())
    .pipe(sass.sync({ outputStyle: "expanded" }))
    .pipe(postcss([autoprefixer(), cssnano()]))
    .pipe(gulp.dest((file) => {
      // blocks/<slug>/assets/scss/foo.scss -> blocks/<slug>/assets/css/
      const baseDir = path.dirname(file.path);
      const cssDir = baseDir.replace(path.sep + "scss", path.sep + "css");
      return cssDir;
    }));
}

export function scriptsBlocks() {
  return gulp.src(paths.jsBlocks, { allowEmpty: true })
    .pipe(plumber())
    .pipe(terser())
    .pipe(rename({ suffix: ".min" }))
    .pipe(gulp.dest((file) => path.dirname(file.path)));
}

export function watch() {
  gulp.watch(paths.scssGlobal, stylesGlobal);
  gulp.watch(["blocks/*/assets/scss/**/*.scss"], stylesBlocks);
  gulp.watch(["blocks/*/assets/js/**/*.js", "!blocks/*/assets/js/**/*.min.js"], scriptsBlocks);
}

export const build = gulp.series(stylesGlobal, stylesBlocks, scriptsBlocks);
export default gulp.series(build, watch);
