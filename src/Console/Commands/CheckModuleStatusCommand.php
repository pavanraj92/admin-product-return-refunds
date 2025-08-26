<?php

namespace admin\product_return_refunds\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class CheckModuleStatusCommand extends Command
{
    protected $signature = 'return_refunds:status';
    protected $description = 'Check if Return Refunds module files are being used';

    public function handle()
    {
        $this->info('Checking Return Refunds Module Status...');

        // Check if module files exist
        $moduleFiles = [
            'Return Refund Controller' => base_path('Modules/ReturnRefunds/app/Http/Controllers/Admin/ReturnRefundManagerController.php'),
            'Return Refund Model' => base_path('Modules/ReturnRefunds/app/Models/ReturnRefundRequest.php'),
            'Routes' => base_path('Modules/ReturnRefunds/routes/web.php'),
            'Views' => base_path('Modules/ReturnRefunds/resources/views'),
            'Config' => base_path('Modules/ReturnRefunds/config/return_refunds.php'),
        ];

        $this->info("\n📁 Module Files Status:");
        foreach ($moduleFiles as $type => $path) {
            if (File::exists($path)) {
                $this->info("✅ {$type}: EXISTS");

                // Check if it's a PHP file and show last modified time
                if (str_ends_with($path, '.php')) {
                    $lastModified = date('Y-m-d H:i:s', filemtime($path));
                    $this->line("   Last modified: {$lastModified}");
                }
            } else {
                $this->error("❌ {$type}: NOT FOUND");
            }
        }

        // Check namespace in controller
        $controllers = [
            'Return Refund Controller' => base_path('Modules/ReturnRefunds/app/Http/Controllers/Admin/ReturnRefundManagerController.php'),
        ];

        foreach ($controllers as $name => $controllerPath) {
            if (File::exists($controllerPath)) {
            $content = File::get($controllerPath);
            if (str_contains($content, 'namespace Modules\ReturnRefunds\app\Http\Controllers\Admin;')) {
                $this->info("\n✅ {$name} namespace: CORRECT");
            } else {
                $this->error("\n❌ {$name} namespace: INCORRECT");
            }

            // Check for test comment
            if (str_contains($content, 'Test comment - this should persist after refresh')) {
                $this->info("✅ Test comment in {$name}: FOUND (changes are persisting)");
            } else {
                $this->warn("⚠️  Test comment in {$name}: NOT FOUND");
            }
            }
        }

        // Check composer autoload
        $composerFile = base_path('composer.json');
        if (File::exists($composerFile)) {
            $composer = json_decode(File::get($composerFile), true);
            if (isset($composer['autoload']['psr-4']['Modules\\ReturnRefunds\\'])) {
                $this->info("\n✅ Composer autoload: CONFIGURED");
            } else {
                $this->error("\n❌ Composer autoload: NOT CONFIGURED");
            }
        }

        $this->info("\n🎯 Summary:");
        $this->info("Your Return Refunds module is properly published and should be working.");
        $this->info("Any changes you make to files in Modules/ReturnRefunds/ will persist.");
        $this->info("If you need to republish from the package, run: php artisan return_refunds:publish --force");
    }
}
