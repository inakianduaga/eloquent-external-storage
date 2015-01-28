# Gulp Tasks

Task are separated by topic 

## build.js

Builds the application 
- uses dependencies in the `/lib` subfolder
- asset js vendor libraries are defined in the `/assets` subfolder

## watch.js

Watch tasks to trigger app recompilation, syncs development server.

## server.js

Contains browsersync-ready development server 

## release.js

Tasks to publish the application to aws S3 
- Configuration credentials must be filled in `/aws/config.json`



