<?php

namespace Tests\Feature;

use Aws\S3\S3Client;
use Exception;
use Tests\TestCase;

class S3DirectTest extends TestCase
{
    /**
     * Test direct connection to S3 using AWS SDK.
     */
    public function test_direct_s3_connection(): void
    {
        // Create S3 client
        $s3Client = new S3Client([
            'version' => 'latest',
            'region' => env('AWS_DEFAULT_REGION'),
            'endpoint' => env('AWS_ENDPOINT'),
            'use_path_style_endpoint' => env('AWS_USE_PATH_STYLE_ENDPOINT', false),
            'credentials' => [
                'key' => env('AWS_ACCESS_KEY_ID'),
                'secret' => env('AWS_SECRET_ACCESS_KEY'),
            ],
            'debug' => false, // Disable debug mode to focus on the specific error message
            'http' => [
                'verify' => false, // Disable SSL certificate verification (not recommended for production)
            ],
        ]);

        try {
            // Try to list objects in the bucket
            $result = $s3Client->listObjects([
                'Bucket' => env('AWS_BUCKET'),
            ]);

            // Output the result
            echo 'Successfully connected to S3 bucket: '.env('AWS_BUCKET').PHP_EOL;
            echo 'Objects in bucket: '.count($result['Contents'] ?? []).PHP_EOL;

            // Try to put a test object
            $testKey = 'test/direct-test-'.time().'.txt';
            $s3Client->putObject([
                'Bucket' => env('AWS_BUCKET'),
                'Key' => $testKey,
                'Body' => 'Test content',
                'ACL' => 'public-read',
            ]);

            echo 'Successfully uploaded test object: '.$testKey.PHP_EOL;

            // Get the object URL
            $objectUrl = $s3Client->getObjectUrl(env('AWS_BUCKET'), $testKey);
            echo 'Object URL: '.$objectUrl.PHP_EOL;

            // Clean up
            $s3Client->deleteObject([
                'Bucket' => env('AWS_BUCKET'),
                'Key' => $testKey,
            ]);

            echo 'Successfully deleted test object: '.$testKey.PHP_EOL;

            $this->assertTrue(true); // Test passed
        } catch (Exception $e) {
            // Output the error
            echo 'Error connecting to S3: '.$e->getMessage().PHP_EOL;
            $this->fail('Failed to connect to S3: '.$e->getMessage());
        }
    }
}
