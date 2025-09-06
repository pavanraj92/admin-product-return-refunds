<?php

namespace admin\product_return_refunds;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class ReturnRefundServiceProvider extends ServiceProvider
{
    public function boot()
    {
        // Load routes, views, migrations from the package  
        $this->loadViewsFrom([
            base_path('Modules/ReturnRefunds/resources/views'), // Published module views first
            resource_path('views/admin/product'), // Published views second
            __DIR__ . '/../resources/views'      // Package views as fallback
        ], 'return_refund');


        if (file_exists(base_path('Modules/ReturnRefunds/config/return_refund.php'))) {
            $this->mergeConfigFrom(base_path('Modules/ReturnRefunds/config/return_refund.php'), 'return_refund.constants');
        } else {
            // Fallback to package config if published config doesn't exist
             $this->mergeConfigFrom(__DIR__ . '/../config/return_refund.php', 'return_refund.constants');
        }

        // Also register module views with a specific namespace for explicit usage
        if (is_dir(base_path('Modules/ReturnRefunds/resources/views'))) {
            $this->loadViewsFrom(base_path('Modules/ReturnRefunds/resources/views'), 'return-refunds-module');
        }
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
        // Also load migrations from published module if they exist
        if (is_dir(base_path('Modules/ReturnRefunds/database/migrations'))) {
            $this->loadMigrationsFrom(base_path('Modules/ReturnRefunds/database/migrations'));
        }

        // Only publish automatically during package installation, not on every request
        // Use 'php artisan return_refunds:publish' command for manual publishing
        // $this->publishWithNamespaceTransformation();

        // Standard publishing for non-PHP files
        $this->publishes([
            __DIR__ . '/../config/' => base_path('Modules/ReturnRefunds/config/'),
            __DIR__ . '/../database/migrations' => base_path('Modules/ReturnRefunds/database/migrations'),
            __DIR__ . '/../resources/views' => base_path('Modules/ReturnRefunds/resources/views/'),
        ], 'return_refund');

        $this->registerAdminRoutes();
    }

    protected function registerAdminRoutes()
    {
        if (!Schema::hasTable('admins')) {
            return; // Avoid errors before migration
        }

        $admin = DB::table('admins')
            ->orderBy('created_at', 'asc')
            ->first();

        $slug = $admin->website_slug ?? 'admin';

        Route::middleware('web')
            ->prefix("{$slug}/admin") // dynamic prefix
            ->group(function () {
                // Load routes from published module first, then fallback to package
                if (file_exists(base_path('Modules/ReturnRefunds/routes/web.php'))) {
                    $this->loadRoutesFrom(base_path('Modules/ReturnRefunds/routes/web.php'));
                } else {
                    $this->loadRoutesFrom(__DIR__ . '/routes/web.php');
                }
            });
    }

    public function register()
    {
        // Register the publish command
        if ($this->app->runningInConsole()) {
            $this->commands([
                \admin\product_return_refunds\Console\Commands\PublishReturnRefundsModuleCommand::class,
                \admin\product_return_refunds\Console\Commands\CheckModuleStatusCommand::class,
                \admin\product_return_refunds\Console\Commands\DebugReturnRefundsCommand::class,
            ]);
        }
    }

    /**
     * Publish files with namespace transformation
     */
    protected function publishWithNamespaceTransformation()
    {
        // Define the files that need namespace transformation
        $filesWithNamespaces = [
            // Controllers
            __DIR__ . '/../src/Controllers/ReturnRefundManagerController.php' => base_path('Modules/ReturnRefunds/app/Http/Controllers/Admin/ReturnRefundManagerController.php'),

            // Models
            __DIR__ . '/../src/Models/ReturnRefundRequest.php' => base_path('Modules/ReturnRefunds/app/Models/ReturnRefundRequest.php'),

            // Routes
            __DIR__ . '/routes/web.php' => base_path('Modules/ReturnRefunds/routes/web.php'),
        ];

        foreach ($filesWithNamespaces as $source => $destination) {
            if (File::exists($source)) {
                // Create destination directory if it doesn't exist
                File::ensureDirectoryExists(dirname($destination));

                // Read the source file
                $content = File::get($source);

                // Transform namespaces based on file type
                $content = $this->transformNamespaces($content, $source);

                // Write the transformed content to destination
                File::put($destination, $content);
            }
        }
    }

    /**
     * Transform namespaces in PHP files
     */
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
            $content = $this->transformControllerNamespaces($content);
        } elseif (str_contains($sourceFile, 'Models')) {
            $content = $this->transformModelNamespaces($content);
        } elseif (str_contains($sourceFile, 'Requests')) {
            $content = $this->transformRequestNamespaces($content);
        } elseif (str_contains($sourceFile, 'routes')) {
            $content = $this->transformRouteNamespaces($content);
        }

        return $content;
    }

    /**
     * Transform controller-specific namespaces
     */
    protected function transformControllerNamespaces($content)
    {
        // Update use statements for models and requests
        $content = str_replace(
            'use admin\\product_return_refunds\\Models\\ReturnRefundRequest;',
            'use Modules\\ReturnRefunds\\app\\Models\\ReturnRefundRequest;',
            $content
        );

        return $content;
    }

    /**
     * Transform model-specific namespaces
     */
    protected function transformModelNamespaces($content)
    {
        // Any model-specific transformations
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
        return $content;
    }

    /**
     * Transform request-specific namespaces
     */
    protected function transformRequestNamespaces($content)
    {
        // Any request-specific transformations
        return $content;
    }

    /**
     * Transform route-specific namespaces
     */
    protected function transformRouteNamespaces($content)
    {
        // Update controller references in routes
        $content = str_replace(
            'admin\\product_return_refunds\\Controllers\\ReturnRefundManagerController',
            'Modules\\ReturnRefunds\\app\\Http\\Controllers\\Admin\\ReturnRefundManagerController',
            $content
        );

        return $content;
    }
}