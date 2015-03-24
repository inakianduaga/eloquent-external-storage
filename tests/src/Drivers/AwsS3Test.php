<?php namespace InakiAnduaga\EloquentExternalStorage\Tests\Drivers;

use InakiAnduaga\EloquentExternalStorage\Tests\AbstractBaseTestCase as BaseTestCase;
use InakiAnduaga\EloquentExternalStorage\Drivers\AwsS3 as StorageDriver;
use Aws\S3\S3Client;
use Illuminate\Support\Facades\Config;
use \Aws\S3\Exception\S3Exception;

class AwsS3Test extends BaseTestCase {

    /**
     * @var StorageDriver
     */
    private $storageDriver;

    /**
     * Random-generated Content
     * @var string
     */
    private $content;

    //-----------//
    //-- Setup --//
    //-----------//

    /**
     * @var FileDriver
     */
    private $fileStorageDriver;

    public function setUp()
    {
        parent::setUp();

        //If AWS environment variables are not set, abort tests
        if(!$this->hasAwsCredentials()) {
            $this->markTestIncomplete('No AWS credentials set or incomplete, skipping AWS S3 Driver test suite.');
        }

        $this->refreshDriver();

        $this->content = $this->generateRandomContent();
    }

    //-----------//
    //-- Tests --//
    //-----------//

    public function testStoreAndFetch()
    {
        $storedPath = $this->storageDriver->store($this->content);

        $this->assertTrue(!empty($storedPath));

        $content = $this->storageDriver->fetch($storedPath);

        $this->assertEquals($content, $this->content);
    }

    /**
     * @expectedException \Aws\S3\Exception\AccessDeniedException
     */
    public function testRemove()
    {
        $storedPath = $this->storageDriver->store($this->content);

        $this->storageDriver->remove($storedPath);

        $content = $this->storageDriver->fetch($storedPath);
    }


    //---------------------//
    //-- Private Methods --//
    //---------------------//

    /**
     * Sets a clean storage driver with configuration & credentials read from environment
     */
    private function refreshDriver()
    {
        Config::set('mocked.config.key', array(
            'key'    => getenv('AWS_S3_KEY'), // Your AWS Access Key ID
            'secret' => getenv('AWS_S3_SECRET'), // Your AWS Secret Access Key
            'region' => getenv('AWS_S3_REGION'),
            's3Bucket' => getenv('AWS_S3_BUCKET'),
            's3BucketSubfolder' => getenv('AWS_S3_BUCKET_SUBFOLDER'),
        ));

        // Inject new file driver and mock config
        $this->storageDriver = new StorageDriver();
        $this->storageDriver->setConfigKey('mocked.config.key');
    }

    /**
     * @return string
     */
    private function generateRandomContent()
    {
        return substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 20);
    }

    /**
     * Checks whether the AWS credentials are set (doesn't check their validity though)
     * @return bool
     */
    private function hasAwsCredentials()
    {
        $mustBeNonEmpty = array(
            'AWS_S3_KEY',
            'AWS_S3_SECRET',
            'AWS_S3_REGION',
            'AWS_S3_BUCKET',
            'AWS_S3_BUCKET_SUBFOLDER'
        );

        foreach($mustBeNonEmpty as $envVar) {
            if(empty(getenv($envVar))) {
                return false;
            }
        }

        return true;
    }

}

