<?php

namespace admin\product_return_refunds\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class PublishReturnRefundsModuleCommand extends Command
{
    protected $signature = 'return_refunds:publish {--force : Force overwrite existing files}';
    protected $description = 'Publish ReturnRefunds module files with proper namespace transformation';

    public function handle()
    {
        $this->info('Publishing ReturnRefunds module files...');

        // Check if module directory exists
        $moduleDir = base_path('Modules/ReturnRefunds');
        if (!File::exists($moduleDir)) {
            File::makeDirectory($moduleDir, 0755, true);
        }

        // Publish with namespace transformation
        $this->publishWithNamespaceTransformation();
        
        // Publish other files
        $this->call('vendor:publish', [
            '--tag' => 'return_refund',
            '--force' => $this->option('force')
        ]);

        // Update composer autoload
        $this->updateComposerAutoload();

        $this->info('ReturnRefunds module published successfully!');
        $this->info('Please run: composer dump-autoload');
    }

    protected function publishWithNamespaceTransformation()
    {
        $basePath = dirname(dirname(__DIR__)); // Go up to packages/admin/products/src

        $filesWithNamespaces = [
            // Controllers
            $basePath . '/Controllers/ReturnRefundManagerController.php' => base_path('Modules/ReturnRefunds/app/Http/Controllers/Admin/ReturnRefundManagerController.php'),
            // Models
            $basePath . '/Models/ReturnRefundRequest.php' => base_path('Modules/ReturnRefunds/app/Models/ReturnRefundRequest.php'),
            // Routes
            $basePath . '/routes/web.php' => base_path('Modules/ReturnRefunds/routes/web.php'),
        ];

        foreach ($filesWithNamespaces as $source => $destination) {
            if (File::exists($source)) {
                File::ensureDirectoryExists(dirname($destination));
                
                $content = File::get($source);
                $content = $this->transformNamespaces($content, $source);
                
                File::put($destination, $content);
                $this->info("Published: " . basename($destination));
            } else {
                $this->warn("Source file not found: " . $source);
            }
        }
    }

    protected function transformNamespaces($content, $sourceFile)
    {
        // Define namespace mappings
        $namespaceTransforms = [
            // Main namespace transformations
            'namespace admin\\product_return_refunds\\Controllers;' => 'namespace Modules\\ReturnRefunds\\app\\Http\\Controllers\\Admin;',
            'namespace admin\\product_return_refunds\\Models;' => 'namespace Modules\\ReturnRefunds\\app\\Models;',

            // Use statements transformations
            'use admin\\product_return_refunds\\Controllers\\' => 'use Modules\\ReturnRefunds\\app\\Http\\Controllers\\Admin\\',
            'use admin\\product_return_refunds\\Models\\' => 'use Modules\\ReturnRefunds\\app\\Models\\',

            // Class references in routes
            'admin\\product_return_refunds\\Controllers\\ReturnRefundManagerController' => 'Modules\\ReturnRefunds\\app\\Http\\Controllers\\Admin\\ReturnRefundManagerController',
        ];

        // Apply transformations
        foreach ($namespaceTransforms as $search => $replace) {
            $content = str_replace($search, $replace, $content);
        }

        // Handle specific file types
        if (str_contains($sourceFile, 'Controllers')) {
            $content = str_replace(
                'use admin\\product_return_refunds\\Models\\ReturnRefundRequest;',
                'use Modules\\ReturnRefunds\\app\\Models\\ReturnRefundRequest;',
                $content
            );
        } elseif (str_contains($sourceFile, 'Models')) {
            // Transform admin_auth namespaces in models
            $content = str_replace(
                'use admin\users\Models\User;',
                'use Modules\\Users\\app\\Models\\User;',
                $content
            );
            $content = str_replace(
                'use admin\products\Models\Product;',
                'use Modules\\Products\\app\\Models\\Product;',
                $content
            );
            $content = str_replace(
                'use admin\products\Models\Order;',
                'use Modules\\Products\\app\\Models\\Order;',
                $content
            );
        }

        return $content;
    }

    protected function updateComposerAutoload()
    {
        $composerFile = base_path('composer.json');
        $composer = json_decode(File::get($composerFile), true);

        // Add module namespace to autoload
        if (!isset($composer['autoload']['psr-4']['Modules\\ReturnRefunds\\'])) {
            $composer['autoload']['psr-4']['Modules\\ReturnRefunds\\'] = 'Modules/ReturnRefunds/app/';

            File::put($composerFile, json_encode($composer, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
            $this->info('Updated composer.json autoload');
        }
    }
}