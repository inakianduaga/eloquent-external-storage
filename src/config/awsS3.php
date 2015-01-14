<?php

/*
|--------------------------------------------------------------------------
| Amazon Aws S3 Sample Driver Configuration
|--------------------------------------------------------------------------
*/
return array(

    /*
    |--------------------------------------------------------------------------
    | Your AWS Credentials
    |--------------------------------------------------------------------------
    |
    | In order to communicate with an AWS service, you must provide your AWS
    | credentials including your AWS Access Key ID and your AWS Secret Key.
    |
    | To use credentials from your credentials file or environment or to use
    | IAM Instance Profile credentials, please remove these config settings from
    | your config or make sure they are null. For more information see:
    | http://docs.aws.amazon.com/aws-sdk-php-2/guide/latest/configuration.html
    |
    */

    // S3 User with only read/write object permissions on tado-website bucket.
    // Remove this credentials once we have IAM credentials on the instance

    'key'    => 'XXX', // Your AWS Access Key ID
    'secret' => 'YYY', // Your AWS Secret Access Key

    /*
    |--------------------------------------------------------------------------
    | AWS Region
    |--------------------------------------------------------------------------
    |
    | Many AWS services are available in multiple regions. You should specify
    | the AWS region you would like to use, but please remember that not every
    | service is available in every region.
    |
    | These are the regions: us-east-1, us-west-1, us-west-2, us-gov-west-1
    | eu-west-1, sa-east-1, ap-northeast-1, ap-southeast-1, ap-southeast-2
    |
    */
    'region' => 'eu-west-1',

    /*
    |--------------------------------------------------------------------------
    | AWS Config File Location
    |--------------------------------------------------------------------------
    |
    | Instead of specifying your credentials and region here, you can specify
    | the location of an AWS SDK for PHP config file to use. These files provide
    | more granular control over what credentials and regions you are using for
    | each service. If you specify a filepath for this configuration setting,
    | the others in this file will be ignored. See the SDK user guide for more
    | information: http://docs.aws.amazon.com/aws-sdk-php-2/guide/latest/configuration.html#using-a-custom-configuration-file
    |
    */
    'config_file' => null,

    /*
    |--------------------------------------------------------------------------
    | AWS S3 Bucket
    |--------------------------------------------------------------------------
    |
    | The S3 bucket name
    |
    */
    's3Bucket' => 'ZZZ',

    /*
    |--------------------------------------------------------------------------
    | S3 bucket subfolder
    |--------------------------------------------------------------------------
    |
    | The S3 "virtual subfolder" where we store the contents
    |
    */
    's3BucketSubfolder' => 'ABC',



);
