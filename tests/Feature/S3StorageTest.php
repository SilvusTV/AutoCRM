<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class S3StorageTest extends TestCase
{
    /**
     * Test if S3 storage is configured correctly.
     */
    public function test_s3_storage_configuration(): void
    {
        // Create a test file
        $content = 'Test file content';
        $path = 'test/test-file.txt';

        // Store the file in S3
        Storage::disk('s3')->put($path, $content, 'public');

        // Check if the file exists
        $this->assertTrue(Storage::disk('s3')->exists($path));

        // Get the file URL
        $url = Storage::disk('s3')->url($path);

        // Output the URL for manual verification
        echo 'File URL: '.$url.PHP_EOL;

        // Clean up
        Storage::disk('s3')->delete($path);
    }
}
