<?php

namespace Modules\S3konnect\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CopyStorageDataToS3Bucket implements ShouldQueue
{
    use Queueable;

    public $timeout = 3600;
    public $tries = 10;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info('inside job');
        $localDisk = Storage::disk('local');
        $s3Disk = Storage::disk('s3');

        //Copy files and directories
        $files = $localDisk->allFiles('public');
        if (!empty($files)) {
            foreach ($files as $file) {
                if(!$s3Disk->exists($file)) {
                    $s3FilePath = Str::ltrim(Str::replace('public/', '', $file), '/');
                    $contents = $localDisk->get($file);
                    $s3Disk->put($s3FilePath, $contents);
                }
            }
            Log::info('files copied');
        }
    }
}
