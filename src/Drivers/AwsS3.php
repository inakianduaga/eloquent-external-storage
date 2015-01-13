<?php namespace InakiAnduaga\EloquentExternalStorage\Drivers;

use InakiAnduaga\EloquentExternalStorage\Drivers\DriverInterface;
use InakiAnduaga\EloquentExternalStorage\Models\ModelWithExternalStorageInterface as Model;
use EloquentExternalStorage\Services\ExtensionGuesser;

use Aws\Laravel\AwsFacade as AWS;
use Carbon\Carbon;

/**
 * Aws S3 storage implementation
 */
class AwsS3 implements DriverInterface {

    /** @var string */
    private $s3Bucket;

    /** var Aws\S3\S3Client */
    private $s3;

    /**
     * The S3 base object path where emails are stored
     * @var string
     */
    private $baseStoragePath = 'Model/'; //TODO: refactor

    function __construct() {
        $this->s3Bucket = Config::get('aws::config.s3Bucket'); //TODO: refactor
        $this->s3 = AWS::get('s3');
    }
    public function generateStoragePath($content)
    {
        $name = md5($content);
        $extension = $this->extensionGuesser($content);
        $subfolder = Carbon::now()->format('Y-m');
        $path = $subfolder.'/'.$name.'.'.$extension;

        return $path;
    }

    /**
     * {@inheritDoc}
     *
     * http://stackoverflow.com/questions/13686316/grabbing-contents-of-object-from-s3-via-php-sdk-2
     */
    public function fetch($path) {

        $result = $this->s3->getObject(array(
                'Bucket' => $this->s3Bucket,
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
    public function store(Model $model, $content)
    {
        $relativePath = $this->generateStoragePath($content);
        $absolutePath = $this->baseStoragePath.$relativePath;

        // Note: The S3 doesS3ObjectExist method has a problem when the object doesn't exist within the sdk, so we skip this check for now
        // if(! $this->doesS3ObjectExist($this->s3Bucket, $absolutePath)) {
        try {
            $this->s3->putObject(array(
                'Bucket'     => $this->s3Bucket,
                'Key'        => $absolutePath,
                'Body'       => $content,
            ));
        }
        catch (S3Exception $e) {
            echo "There was an error uploading the file.\n";
        }
        // }

        return $model->setPath($absolutePath); //TODO: refactor
    }

    public function remove($path)
    {
        return $this->s3->deleteObject(array(
                'Bucket' => $this->s3Bucket,
                'Key'    => $path,
            )
        );
    }

    /**
     * Checks whether an S3 object exists
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

} 