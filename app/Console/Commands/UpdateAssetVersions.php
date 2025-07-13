<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class UpdateAssetVersions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'assets:version {version?} {--all : Update all assets to the same version} {--file= : Update specific file version}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update asset versions for cache busting';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $version = $this->argument('version') ?? '1.0.' . time();
        $configPath = config_path('assets.php');
        
        if (!File::exists($configPath)) {
            $this->error('Assets config file not found!');
            return 1;
        }

        $config = require $configPath;

        if ($this->option('all')) {
            // Update all versions
            $config['version'] = $version;
            foreach ($config['versions'] as $asset => $currentVersion) {
                $config['versions'][$asset] = $version;
            }
            
            $this->info("Updated all assets to version: {$version}");
        } elseif ($file = $this->option('file')) {
            // Update specific file
            if (isset($config['versions'][$file])) {
                $config['versions'][$file] = $version;
                $this->info("Updated {$file} to version: {$version}");
            } else {
                $this->error("Asset {$file} not found in config!");
                return 1;
            }
        } else {
            // Update default version
            $config['version'] = $version;
            $this->info("Updated default asset version to: {$version}");
        }

        // Write updated config
        $configContent = "<?php\n\nreturn " . var_export($config, true) . ";\n";
        File::put($configPath, $configContent);

        $this->info('Asset versions updated successfully!');
        
        return 0;
    }
} 