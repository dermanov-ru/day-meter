<?php

use Illuminate\Database\Migrations\Migration;
use Minishlink\WebPush\WebPush;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Check if VAPID keys are already set
        $publicKey = config('push.public_key');
        $privateKey = config('push.private_key');

        if (!empty($publicKey) && !empty($privateKey)) {
            return; // Keys already exist, skip
        }

        // Generate VAPID keys using WebPush library
        try {
            $keys = WebPush::generateVapidKeys();
            $publicKey = $keys['publicKey'];
            $privateKey = $keys['privateKey'];
        } catch (\Exception $e) {
            throw new \Exception('Failed to generate VAPID keys: ' . $e->getMessage());
        }

        // Update .env file
        $envPath = base_path('.env');
        
        if (!file_exists($envPath)) {
            // If .env doesn't exist, create it
            file_put_contents($envPath, '');
        }
        
        $envContent = file_get_contents($envPath);

        // Check if keys already exist in .env
        if (strpos($envContent, 'PUSH_PUBLIC_KEY=') !== false) {
            $envContent = preg_replace(
                '/PUSH_PUBLIC_KEY=.*/i',
                "PUSH_PUBLIC_KEY={$publicKey}",
                $envContent
            );
        } else {
            $envContent .= "\nPUSH_PUBLIC_KEY={$publicKey}";
        }

        if (strpos($envContent, 'PUSH_PRIVATE_KEY=') !== false) {
            $envContent = preg_replace(
                '/PUSH_PRIVATE_KEY=.*/i',
                "PUSH_PRIVATE_KEY={$privateKey}",
                $envContent
            );
        } else {
            $envContent .= "\nPUSH_PRIVATE_KEY={$privateKey}";
        }

        file_put_contents($envPath, $envContent);

        // Clear config cache to load new values
        if (file_exists(base_path('bootstrap/cache/config.php'))) {
            unlink(base_path('bootstrap/cache/config.php'));
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Don't remove VAPID keys on rollback
    }
};
