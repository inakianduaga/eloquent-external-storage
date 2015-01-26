<?php namespace InakiAnduaga\EloquentExternalStorage\Drivers;

use InakiAnduaga\EloquentExternalStorage\Drivers\AbstractDriver;

use Aws\S3\S3Client;
use Aws\S3\Exception\S3Exception;

/**
 * Aws S3 storage implementation
 */
class AwsS3 extends AbstractDriver {

    protected $directorySeparator = '_';

    protected $configKey = 'inakianduaga/eloquent-external-storage::awsS3';

    /**
     * An s3 client configured instance
     * @var S3Client
     */
    private $s3;

    /**
     * Initialize the S3 Client
     */
    function __construct() {

        parent::__construct();

        $this->initializeConfiguredS3Client();
    }

    /**
     * {@inheritdoc}
     *
     * Reinitializes the s3 client after config path has been set
     */
    public function setConfigKey($key)
    {
        $this->configKey = $key;

        $this->initializeConfiguredS3Client();

        return $this;
    }

    /**
     * {@inheritDoc}
     *
     * http://stackoverflow.com/questions/13686316/grabbing-contents-of-object-from-s3-via-php-sdk-2
     */
    public function fetch($path) {

        $result = $this->s3->getObject(array(
                'Bucket' => $this->getConfigRelativeKey('s3Bucket'),
                'Key'    => $path,
            )
        );

        $body = $result->get('Body');
        $body->rewind();

        $content = $body->read($result['ContentLength']);

        return $content;
    }

    /**
     * {@inheritDoc}
     *
     * http://docs.aws.amazon.com/AmazonS3/latest/dev/UploadObjSingleOpPHP.html
     */
    public function store($content)
    {
        $relativePath = $this->generateStoragePath($content);
        $absolutePath = $this->getConfigRelativeKey('s3BucketSubfolder').'/'.$relativePath;

        // Note: The S3 doesS3ObjectExist method has a problem when the object doesn't exist within the sdk, so we skip this check for now
        // if(! $this->doesS3ObjectExist($this->getConfigRelativeKey('s3Bucket'),, $absolutePath)) {
        try {
            $this->s3->putObject(array(
                'Bucket'     => $this->getConfigRelativeKey('s3Bucket'),
                'Key'        => $absolutePath,
                'Body'       => $content,
            ));
        }
        catch (S3Exception $e) {
            echo "There was an error uploading the file: ". $e->getMessage().PHP_EOL;
        }
        // }

        return $absolutePath;
    }

    public function remove($path)
    {
        return $this->s3->deleteObject(array(
                'Bucket' => $this->getConfigRelativeKey('s3Bucket'),
                'Key'    => $path,
            )
        );
    }

    /**
     * Checks whether an S3 object exists
     *
     * @param string $bucket
     * @param string $key
     *
     * @return bool
     * @throws S3Exception if there is a problem
     */
    private function doesS3ObjectExist($bucket, $key)
    {
        try {
            return $this->s3->doesObjectExist($bucket, $key);
        }
        catch (S3Exception $e) {
            echo "There was a problem trying to locate S3 object: $key.\n";
        }
    }

    /**
     *
     */
    private function initializeConfiguredS3Client()
    {
        $this->s3 = S3Client::factory(array(
            'key'    => $this->getConfigRelativeKey('key'),
            'secret' => $this->getConfigRelativeKey('secret'),
            'region' => $this->getConfigRelativeKey('region'),
        ));
    }


} 