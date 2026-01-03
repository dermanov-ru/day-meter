<?php

namespace App\Console\Commands;

use App\Services\WebPushService;
use Illuminate\Console\Command;

class GenerateVapidKeys extends Command
{
    protected $signature = 'vapid:generate';
    protected $description = 'Generate VAPID keys for Web Push notifications';

    public function handle(): int
    {
        try {
            $keys = WebPushService::generateVapidKeys();

            $this->info('VAPID keys generated successfully!');
            $this->newLine();
            $this->info('Add the following to your .env file:');
            $this->newLine();
            $this->line("PUSH_PUBLIC_KEY={$keys['publicKey']}");
            $this->line("PUSH_PRIVATE_KEY={$keys['privateKey']}");
            $this->newLine();

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error('Failed to generate VAPID keys: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
