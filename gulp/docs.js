'use strict';

var gulp = require('gulp'),
    $ = require('gulp-load-plugins')({
      pattern: ['gulp-*', 'browser-sync']
    }),
    apigen = {
      docsFolder : 'docs'
    };

/**
 * Launches a Browser sync file server with the generated docs.
 */
function serveDocs() {

  //BS Default Options
  var bsDefaultOptions = {
        server : {
          baseDir : apigen.docsFolder
        },
        startPath: '/'
      };

  if(!$.browserSync.active) {
    $.browserSync.init('', bsDefaultOptions);
  }

}

gulp.task('docs', 'Generate package docs', function() {

  gulp.src('').pipe($.shell([
      'php apigen.phar generate -s src -d ' + apigen.docsFolder + ' --todo --download --access-levels="[public, protected, private]" --template-theme="bootstrap"',
    ], {
      quiet : false
    })
  )
  .on('end', function() {
      serveDocs();
  });


}, {
  options: {
    'generateCoverage [null]': 'Whether we generate a coverage report along with running the test [coverageClover|coverageCrap4j|coverageHtml|coveragePhp|coverageXml]'
  }
});


