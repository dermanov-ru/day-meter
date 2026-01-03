<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\ChronicleExportService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class ExportChronicleCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'chronicle:export 
                            {--from= : Start date (YYYY-MM-DD)}
                            {--to= : End date (YYYY-MM-DD)}
                            {--user-id=1 : User ID to export (default: 1)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Export chronicle to markdown file for AI analysis';

    /**
     * Execute the console command.
     */
    public function handle(ChronicleExportService $exportService): int
    {
        // Parse dates
        try {
            $from = $this->option('from') 
                ? Carbon::createFromFormat('Y-m-d', $this->option('from'))
                : Carbon::now()->subMonth()->startOfMonth();

            $to = $this->option('to')
                ? Carbon::createFromFormat('Y-m-d', $this->option('to'))
                : Carbon::now()->subMonth()->endOfMonth();
        } catch (\Exception $e) {
            $this->error('Invalid date format. Use YYYY-MM-DD');
            return 1;
        }

        // Get user
        $userId = (int) $this->option('user-id');
        $user = User::find($userId);

        if (!$user) {
            $this->error("User with ID {$userId} not found");
            return 1;
        }

        // Export chronicle
        try {
            $path = $exportService->export($user, $from, $to);
            $this->info('');
            $this->info('Export completed.');
            $this->info("File saved to: {$path}");
            $this->info('');
            return 0;
        } catch (\Exception $e) {
            $this->error('Export failed: ' . $e->getMessage());
            return 1;
        }
    }
}
