<?php

namespace Modules\SpaceKonnect\Http\Controllers;

use App\Http\Controllers\Controller;
use Aws\S3\Exception\S3Exception;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Larabuild\Optionbuilder\Facades\Settings;
use League\Flysystem\UnableToListContents;
use Modules\SpaceKonnect\Jobs\CopyStorageDataToDoSpace;

class SpaceKonnectController extends Controller
{
    /** @var string */
    protected $envPath;

    /** @var string */
    protected $envExamplePath;

    public function __construct()
    {
        $this->envPath = base_path('.env');
        $this->envExamplePath = base_path('.env.example');
    }

    /**
     * Update Digital Ocean Space settings and manage storage configuration
     *
     * This method handles two main responsibilities:
     * 1. Storage Configuration: Switches between local and DO Spaces based on settings and enabled status
     * 2. Data Synchronization: Triggers data copy to DO Spaces when settings are correct
     *
     * @return array{success: bool, message: string}
     */
    public function updateDospaceSettings(): array
    {
        if (!$this->checkEnvWritePermission()) {
            return [
                'success' => false,
                'message' => __('spacekonnect::spacekonnect.permission_denied')
            ];
        }

        $spaceSettingsStatus = $this->isDoSpaceSettingsCorect();
        $isSpaceEnabled = !empty(setting('_space.space_enabled')) && setting('_space.space_enabled') == 'on';

        // Handle storage configuration
        if ($spaceSettingsStatus === true && $isSpaceEnabled === true) {
            $this->switchToSpacesStorage();
        } else {
            $this->switchToLocalStorage();
        }

        // Handle data synchronization
        if ($spaceSettingsStatus === true) {
            dispatch(new CopyStorageDataToDoSpace());
            $this->updateSpaceEnv();
        }

        if ($spaceSettingsStatus !== true) {
            Log::info($spaceSettingsStatus);
            return [
                'success' => false,
                'message' => __('spacekonnect::spacekonnect.dospace_insufficient_permissions')
            ];
        }

        return [
            'success' => true,
            'message' => __('spacekonnect::spacekonnect.do_space_correct')
        ];
    }

    /**
     * Switch to local storage and update settings
     */
    private function switchToLocalStorage(): void
    {
        $this->updateFileSystemEnv('local');
        Settings::set('_space', 'space_enabled', []);
    }

    /**
     * Switch to DO Spaces storage and update settings
     */
    private function switchToSpacesStorage(): void
    {
        $this->updateFileSystemEnv('spaces');
        Settings::set('_space', 'space_enabled', 'on');
    }

    /**
     * Update filesystem disk in environment file
     */
    private function updateFileSystemEnv(string $disk): void
    {
        $env = $this->getEnvContent();
        $settings = ['FILESYSTEM_DISK' => $disk];
        $updatedEnv = $this->updateEnvContent($env, $settings);
        $this->saveEnvFile($updatedEnv);
    }

    /**
     * Update Space configuration in environment file
     */
    private function updateSpaceEnv(): void
    {
        $env = $this->getEnvContent();
        $updatedEnv = $this->updateEnvContent($env, $this->getDoSpaceSettings());
        $this->saveEnvFile($updatedEnv);
    }

    /**
     * Get DO Spaces configuration settings
     *
     * @return array<string, string|null>
     */
    private function getDoSpaceSettings(): array
    {
        $region = setting('_space.default_region');
        $bucket = setting('_space.bucket_name');

        return [
            'DO_SPACES_KEY' => setting('_space.access_key_id'),
            'DO_SPACES_SECRET' => setting('_space.secret_access_key'),
            'DO_SPACES_ENDPOINT' => "https://{$region}.digitaloceanspaces.com",
            'DO_SPACES_REGION' => $region,
            'DO_SPACES_BUCKET' => $bucket,
            'DO_SPACES_URL' => "https://{$bucket}.{$region}.digitaloceanspaces.com"
        ];
    }

    /**
     * Verify DO Spaces settings and connectivity
     *
     * @return bool|string True if settings are correct, 'permissions_denied' or false otherwise
     */
    private function isDoSpaceSettingsCorect()
    {
        $this->configureDoSpace();
        
        try {
            return $this->canListFiles() && $this->testFileOperations();
        } catch (\Exception $e) {
            return $this->handleSpaceException($e);
        }
    }

    /**
     * Configure DO Spaces settings in runtime
     */
    private function configureDoSpace(): void
    {
        $region = setting('_space.default_region');
        $bucket = setting('_space.bucket_name');

        config([
            'filesystems.default' => 'spaces',
            'filesystems.disks.spaces.key' => setting('_space.access_key_id'),
            'filesystems.disks.spaces.secret' => setting('_space.secret_access_key'),
            'filesystems.disks.spaces.endpoint' => "https://{$region}.digitaloceanspaces.com",
            'filesystems.disks.spaces.region' => $region,
            'filesystems.disks.spaces.bucket' => $bucket,
            'filesystems.disks.spaces.url' => "https://{$bucket}.{$region}.digitaloceanspaces.com",
        ]);
    }

    /**
     * Test if files can be listed in the Space
     */
    private function canListFiles(): bool
    {
        try {
            Storage::disk('spaces')->allFiles();
            return true;
        } catch (UnableToListContents $e) {
            throw $e;
        }
    }

    /**
     * Test file operations in the Space
     */
    private function testFileOperations(): bool
    {
        $tempFile = 'temp-connectivity-check.txt';
        
        try {
            if (Storage::disk('spaces')->put($tempFile, 'connection successful')) {
                Storage::disk('spaces')->delete($tempFile);
                return true;
            }
        } catch (\Exception $e) {
            Log::error('DO Space file operation test failed: ' . $e->getMessage());
        }
        
        return false;
    }

    /**
     * Handle exceptions from Space operations
     */
    private function handleSpaceException(\Exception $e): bool|string
    {
        if ($e instanceof UnableToListContents || 
            ($e instanceof S3Exception && $e->getStatusCode() == 403)) {
            return 'permissions_denied';
        }
        
        Log::error('DO Space connection error: ' . $e->getMessage());
        return false;
    }

    /**
     * Update environment file content
     */
    private function updateEnvContent(string $env, array $settings): string
    {
        return collect(explode("\n", $env))
            ->map(function ($row) use ($settings) {
                foreach ($settings as $key => $value) {
                    if (strpos($row, $key . '=') === 0) {
                        return $key . "=\"{$value}\"";
                    }
                }
                return $row;
            })->implode("\n");
    }

    /**
     * Save content to environment file
     */
    private function saveEnvFile(string $content): void
    {
        try {
            file_put_contents($this->envPath, $content);
            Artisan::call('config:clear');
        } catch (\Exception $e) {
            Log::error('Failed to update env file: ' . $e->getMessage());
        }
    }

    /**
     * Get environment file content
     */
    private function getEnvContent(): string
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
     * Check if environment file is writable
     */
    private function checkEnvWritePermission(): bool
    {
        return is_writable($this->envPath);
    }
}
