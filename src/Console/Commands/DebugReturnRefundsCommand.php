<?php

namespace admin\product_return_refunds\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class DebugReturnRefundsCommand extends Command
{
    protected $signature = 'return_refunds:debug';
    protected $description = 'Debug Return Refunds module routing and view resolution';

    public function handle()
    {
        $this->info('🔍 Debugging Return Refunds Module...');

        // Check route file loading
        $this->info("\n📍 Route Files:");
        $moduleRoutes = base_path('Modules/ReturnRefunds/routes/web.php');
        if (File::exists($moduleRoutes)) {
            $this->info("✅ Module routes found: {$moduleRoutes}");
            $this->info("   Last modified: " . date('Y-m-d H:i:s', filemtime($moduleRoutes)));
        } else {
            $this->error("❌ Module routes not found");
        }

        $packageRoutes = base_path('packages/admin/product_return_refunds/src/routes/web.php');
        if (File::exists($packageRoutes)) {
            $this->info("✅ Package routes found: {$packageRoutes}");
            $this->info("   Last modified: " . date('Y-m-d H:i:s', filemtime($packageRoutes)));
        } else {
            $this->error("❌ Package routes not found");
        }
        
        // Check view loading priority
        $this->info("\n👀 View Loading Priority:");
        $viewPaths = [
            'Module views' => base_path('Modules/ReturnRefunds/resources/views'),
            'Published views' => resource_path('views/admin/product_return_refunds'),
            'Package views' => base_path('packages/admin/product_return_refunds/resources/views'),
        ];
        
        foreach ($viewPaths as $name => $path) {
            if (File::exists($path)) {
                $this->info("✅ {$name}: {$path}");
            } else {
                $this->warn("⚠️  {$name}: NOT FOUND - {$path}");
            }
        }
        
        // Check controller resolution
        $this->info("\n🎯 Controller Resolution:");
       $controllerClass = 'Modules\\ReturnRefunds\\app\\Http\\Controllers\\Admin\\ReturnRefundManagerController';

        if (class_exists($controllerClass)) {
            $this->info("✅ Controller class found: {$controllerClass}");
            
            $reflection = new \ReflectionClass($controllerClass);
            $this->info("   File: " . $reflection->getFileName());
            $this->info("   Last modified: " . date('Y-m-d H:i:s', filemtime($reflection->getFileName())));
        } else {
            $this->error("❌ Controller class not found: {$controllerClass}");
        }

       // Show current routes
        $this->info("\n🛣️  Current Routes:");
        $routes = Route::getRoutes();
        $productRoutes = [];

        foreach ($routes as $route) {
            $action = $route->getAction();
            if (isset($action['controller'])) {
            if (str_contains($action['controller'], 'ReturnRefundManagerController')) {
                $productRoutes[] = [
                'uri' => $route->uri(),
                'methods' => implode('|', $route->methods()),
                'controller' => $action['controller'],
                'name' => $route->getName(),
                ];
            }
            }
        }
        
        if (!empty($productRoutes)) {
            $this->table(['URI', 'Methods', 'Controller', 'Name'], $productRoutes);
        } else {
            $this->warn("No shipping routes found.");
        }
    }
}
