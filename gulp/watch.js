'use strict';

var gulp = require('gulp'),
    notifications = require('./lib/notifications'),
    events = require('./lib/events');

//---------------
// Tasks
//---------------

gulp.task('tdd', 'Runs unit tests when file changes are detected', function () {
  gulp.watch('**/*.php', ['test']);
});
