<?php namespace InakiAnduaga\EloquentExternalStorage\Tests\Drivers;

use InakiAnduaga\EloquentExternalStorage\Tests\AbstractBaseTestCase as BaseTestCase;
use InakiAnduaga\EloquentExternalStorage\Drivers\AwsS3 as StorageDriver;
use Aws\S3\S3Client;
use Illuminate\Support\Facades\Config;

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

        $this->refreshDriver();

        $this->content = $this->generateRandomContent();
    }

    //-----------//
    //-- Tests --//
    //-----------//

    public function testStoreAndFetch()
    {
        $storedPath = $this->storageDriver->store($this->content);
        var_dump($storedPath);

        $this->assertTrue(!empty($storedPath));

        $content = $this->storageDriver->fetch($storedPath);

        $this->assertEquals($content, $this->content);
    }

    public function testRemove()
    {
        $storedPath = $this->storageDriver->store($this->content);

        $this->storageDriver->remove($storedPath);

        $content = $this->storageDriver->fetch($storedPath);

        //HERE THERE SHOULD BE SOME EXCEPTION OR ERROR SINCE THE FILE SHOULDN'T BE REACHABLE
    }


    //---------------------//
    //-- Private Methods --//
    //---------------------//

    /**
     * Sets a clean storage driver with configuration & credentials read from environment
     */
    private function refreshDriver()
    {
        // Inject new file driver and mock config
        $this->storageDriver = new StorageDriver();
        $this->storageDriver->setConfigKey('mocked.config.key');

        Config::set('mocked.config.key', array(
            'key'    => getenv('AWS_S3_KEY'), // Your AWS Access Key ID
            'secret' => getenv('AWS_S3_SECRET'), // Your AWS Secret Access Key
            'region' => getenv('AWS_S3_REGION'),
            's3Bucket' => getenv('AWS_S3_BUCKET'),
            's3BucketSubfolder' => getenv('AWS_S3_BUCKET_SUBFOLDER'),
        ));
    }

    private function generateRandomContent()
    {
        return substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 20);
    }


}

