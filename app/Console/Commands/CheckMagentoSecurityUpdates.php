<?php

namespace App\Console\Commands;

use App\Http\Controllers\SecurityMonitorController;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CheckMagentoSecurityUpdates extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'security:check-updates';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check for new Magento security updates from Adobe';

    /**
     * Execute the console command.
     */
    public function handle(SecurityMonitorController $controller)
    {
        $this->info('Checking for Magento security updates...');
        Log::info('Starting Magento security update check via command...');

        $result = $controller->checkForUpdates();

        if ($result['success']) {
            $this->info('Security update check completed successfully.');
            $this->info('Bulletins processed: '.$result['bulletins_processed']);
            Log::info('Magento security update check completed. Bulletins processed: '.$result['bulletins_processed']);
        } else {
            $this->error('Security update check failed: '.$result['message']);
            Log::error('Magento security update check failed: '.$result['message']);
        }

        return 0;
    }
}
