<?php

namespace Modules\SpaceKonnect\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CopyStorageDataToDoSpace implements ShouldQueue
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
        $localDisk = Storage::disk('local');
        $spaceDisk = Storage::disk('spaces');

        // Copy files and directories
        $files = $localDisk->allFiles('public');
        
        if (!empty($files)) {
            $batchSize = 1000;
            $chunks = array_chunk($files, $batchSize);
            
            foreach ($chunks as $chunk) {
                $this->copyFilesToDOSpaces($chunk, $localDisk, $spaceDisk);
            }

            Log::info('All files copied successfully');
        }
    }

    /**
     * Copy a batch of files to the cloud storage.
     */
    private function copyFilesToDOSpaces(array $files, $localDisk, $spaceDisk): void
    {
        $toUpload = [];
        
        // Collect files that do not exist in spaceDisk
        foreach ($files as $file) {
            // if (!$spaceDisk->exists($file)) {
                $s3FilePath = Str::ltrim(Str::replace('public/', '', $file), '/');
                $contents = $localDisk->get($file);
                $toUpload[] = [
                    'path' => $s3FilePath,
                    'contents' => $contents
                ];
            // }
        }

        // Upload files in bulk (if applicable)
        foreach ($toUpload as $file) {
            $spaceDisk->put($file['path'], $file['contents']);
        }
    }
}
