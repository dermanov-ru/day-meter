<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Process;

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

        // Generate VAPID keys using Python
        $result = Process::run('python3 -c "
import base64
from cryptography.hazmat.primitives import serialization
from cryptography.hazmat.primitives.asymmetric import ec
from cryptography.hazmat.backends import default_backend

private_key = ec.generate_private_key(ec.SECP256R1(), default_backend())
public_key = private_key.public_key()

public_bytes = public_key.public_bytes(
    encoding=serialization.Encoding.X962,
    format=serialization.PublicFormat.UncompressedPoint
)

private_bytes = private_key.private_numbers().private_value
private_bytes_data = private_bytes.to_bytes(32, \'big\')

public_key_b64 = base64.urlsafe_b64encode(public_bytes).decode().rstrip(\'=\')
private_key_b64 = base64.urlsafe_b64encode(private_bytes_data).decode().rstrip(\'=\')

print(f\'{public_key_b64}|{private_key_b64}\')
"');

        if (!$result->successful()) {
            throw new \Exception('Failed to generate VAPID keys: ' . $result->errorOutput());
        }

        [$publicKey, $privateKey] = explode('|', trim($result->output()));

        // Update .env file
        $envPath = base_path('.env');
        $envContent = file_get_contents($envPath);

        $envContent = preg_replace(
            '/PUSH_PUBLIC_KEY=.*/i',
            "PUSH_PUBLIC_KEY={$publicKey}",
            $envContent
        );

        $envContent = preg_replace(
            '/PUSH_PRIVATE_KEY=.*/i',
            "PUSH_PRIVATE_KEY={$privateKey}",
            $envContent
        );

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
