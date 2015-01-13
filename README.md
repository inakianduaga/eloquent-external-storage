# eloquent-external-storage
Adds external storage to an eloquent model


### Publish package configuration

In the laravel installation root folder, run

`php artisan config:publish inakianduaga/eloquent-external-storage`

You can then modify the placeholder values in the file `app/config/packages/inakianduaga/eloquent-external-storage/awsS3.php`, `app/config/packages/inakianduaga/eloquent-external-storage/file.php`
for the standard drivers

#### Supporting multiple models with external storage
 
If you need to associate external storage to different models using different configurations, you can set the configuration key for each of the driver instances using the `@setConfigKey` method on the chosen driver.

@TODO: Expand/Add examples
