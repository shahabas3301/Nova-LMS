<?php

namespace Modules\S3konnect\Http\Controllers;

use App\Http\Controllers\Controller;
use Aws\S3\Exception\S3Exception;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Larabuild\Optionbuilder\Facades\Settings;
use League\Flysystem\UnableToListContents;
use Modules\S3konnect\Jobs\CopyStorageDataToS3Bucket;

class S3KonnectController extends Controller
{

    protected $envPath;
    protected $envExamplePath;

    public function __construct()
    {
        $this->envPath = base_path('.env');
        $this->envExamplePath = base_path('.env.example');
    }


    public function updateS3BucketSettings()
    {

        if ($this->isS3SettingsCorect() === 'permissions_denied') {
            return [
                'success' => false,
                'message' => __('s3konnect::s3konnect.s3_insufficient_permissions')
            ];
        }

        if ($this->isS3SettingsCorect()) {
            dispatch(new CopyStorageDataToS3Bucket());

            $envs3Status = $this->updateS3Env();

            $fileSystem = !empty(setting('_storage.s3_enabled')) && setting('_storage.s3_enabled') == 'on' ? 's3' : 'local';

            $this->updateFileSystemEnv($fileSystem);

            if ($envs3Status === 'env_updated') {
                return [
                    'success' => true,
                    'message' => __('s3konnect::s3konnect.s3_bucket_correct')
                ];
            } elseif ($envs3Status === 'permission_denied') {
                return [
                    'success' => false,
                    'message' => __('s3konnect::s3konnect.permission_denied')
                ];
            }
        } else {
            $this->updateFileSystemEnv('local');
            Settings::set('_storage', 's3_enabled', []);

            return [
                'success' => false,
                'message' => __('s3konnect::s3konnect.s3_bucket_wrong'),
            ];
        }
    }

    /**
     * Update the S3 bucket settings in .env.
     */
    public function updateS3Env()
    {
        if (!is_writable($this->envPath)) {
            return 'permission_denied';
        }

        $env = $this->getEnvContent();

        $s3BucketSetting = [
            'AWS_ACCESS_KEY_ID'         => setting('_storage.access_key_id') ?? null,
            'AWS_SECRET_ACCESS_KEY'     => setting('_storage.secret_access_key') ?? null,
            'AWS_DEFAULT_REGION'        => setting('_storage.default_region') ?? null,
            'AWS_BUCKET'                => setting('_storage.bucket_name') ?? null,
        ];

        $rows = explode("\n", $env);
        foreach ($rows as &$row) {
            foreach ($s3BucketSetting as $key => $value) {
                if (strpos($row, $key . '=') === 0) {
                    $row = $key . "={$value}";
                }
            }
        }
        unset($row);
        $env = implode("\n", $rows);

        try {
            file_put_contents($this->envPath, $env);
            Artisan::call('config:clear');

            return 'env_updated';
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return 'permission_denied';
        }
    }

    /**
     * Update the S3 bucket settings in .env.
     */
    public function updateFileSystemEnv($disk = 'local')
    {
        if (!is_writable($this->envPath)) {
            return 'permission_denied';
        }

        $env = $this->getEnvContent();

        $s3BucketSetting = [
            'FILESYSTEM_DISK'           => $disk,
        ];

        $rows = explode("\n", $env);
        foreach ($rows as &$row) {
            foreach ($s3BucketSetting as $key => $value) {
                if (strpos($row, $key . '=') === 0) {
                    $row = $key . "={$value}";
                }
            }
        }
        unset($row);
        $env = implode("\n", $rows);

        try {
            file_put_contents($this->envPath, $env);
            Artisan::call('config:clear');
        } catch (\Exception $e) {
            Log::error($e->getMessage());
        }
    }


    /**
     * Get the content of the .env file.
     *
     * @return string
     */
    protected function getEnvContent()
    {
        if (!file_exists($this->envPath)) {
            if (file_exists($this->envExamplePath)) {
                copy($this->envExamplePath, $this->envPath);
            } else {
                touch($this->envPath);
            }
        }

        return file_get_contents($this->envPath);
    }

    /**
     * Check the connectivity status of the S3 bucket.
     *
     * @return bool
     */
    private function isS3SettingsCorect()
    {
        // Set S3 config dynamically
        config([
            'filesystems.default'          => 's3',
            'filesystems.disks.s3.key'      => setting('_storage.access_key_id'),
            'filesystems.disks.s3.secret'   => setting('_storage.secret_access_key'),
            'filesystems.disks.s3.region'   => setting('_storage.default_region'),
            'filesystems.disks.s3.bucket'   => setting('_storage.bucket_name'),
        ]);

        $tempFileName = 'temp-connectivity-check.txt';

        try {
            $files = Storage::disk('s3')->allFiles();
        } catch (UnableToListContents $e) {
            return 'permissions_denied';
        } catch (S3Exception $e) {
            if ($e->getStatusCode() == 403) {
                return 'permissions_denied';
            }
            return false;
        } catch (\Exception $e) {
            return 'permissions_denied';
        }

        // If able to list files, proceed to check file upload
        try {
            if (Storage::disk('s3')->put($tempFileName, 'connection successful')) {
                Storage::disk('s3')->delete($tempFileName);
                return true;
            }
        } catch (\Exception $e) {
            return false;
        }

        return false;
    }
}
