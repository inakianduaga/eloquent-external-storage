'use strict';

var gulp = require('gulp'),
    $ = require('gulp-load-plugins')({
      pattern: ['gulp-*', 'browser-sync']
    }),
    environment = require('./lib/environment'),
    notifications = require('./lib/notifications'),
    phpunit = {
      coverageFolder : 'coverage',
      mainConfigFile : 'phpunit.xml'
    };

/**
 * Build phpunit CLI options
 */
function generatePHPUnitCLIOptions() {

  var phpunitCLIOptions = {
    notify : environment.get('notify', true)
  }

  //Add coverage options
  if(environment.get('generateCoverage', false)) {
    phpunitCLIOptions[environment.get('generateCoverage', false)] = phpunit.coverageFolder;
  }

  return phpunitCLIOptions;
}

/**
 * Launches a Browser sync file server with the coverage report.
 * - Autoreloads when coverage report changes
 */
function serveCoverageReport() {

  //BS Default Options
  var bsDefaultOptions = {
        //proxy: environment.get('proxy', 'laravel.localhost'),
        server : {
          baseDir : phpunit.coverageFolder
        },
        startPath: '/'
      },
      bsWatchFiles = [phpunit.coverageFolder + '/**/*'];

  if(!$.browserSync.active) {
    $.browserSync.init(bsWatchFiles, bsDefaultOptions);
  }

}

gulp.task('test', 'Run unit tests (once)', function() {

  gulp.src(phpunit.mainConfigFile)

    //Run phpunit
    .pipe($.phpunit('./vendor/bin/phpunit', generatePHPUnitCLIOptions()))

    //Trigger notification on error
    .on('error', function() {
      gulp.src('.').pipe(notifications.notifyPipe('Tests Failed', false)());
    })

    //Optionally launch coverage server if we have build coverage
    .on('end', function() {
      if(environment.get('generateCoverage', false)) {
        console.log('serving coverage');
        serveCoverageReport();
      }
    })

    //Trigger notification on success
    .pipe(notifications.notifyPipe('Tests Passed', true)())


}, {
  options: {
    'generateCoverage [null]': 'Whether we generate a coverage report along with running the test [coverageClover|coverageCrap4j|coverageHtml|coveragePhp|coverageXml]'
  }
});


