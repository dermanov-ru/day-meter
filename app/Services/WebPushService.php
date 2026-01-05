<?php

namespace App\Services;

use App\Models\PushSubscription;
use Minishlink\WebPush\WebPush;
use Minishlink\WebPush\Subscription;

class WebPushService
{
    private WebPush $webPush;

    public function __construct()
    {
        $vapidPublicKey = config('push.public_key');
        $vapidPrivateKey = config('push.private_key');

        $this->webPush = new WebPush([
            'VAPID' => [
                'subject' => config('push.subject'),
                'publicKey' => $vapidPublicKey,
                'privateKey' => $vapidPrivateKey,
            ],
        ]);
    }

    /**
     * Send push notification to a subscription.
     *
     * @param  PushSubscription  $subscription
     * @param  array  $payload
     * @return bool
     */
    public function send(PushSubscription $subscription, array $payload): bool
    {
        try {
            $webPushSubscription = Subscription::create($subscription->getSubscriptionData());

            // Send the notification
            $this->webPush->sendOneNotification(
                $webPushSubscription,
                json_encode($payload)
            );

            // Check for errors in the flush
            foreach ($this->webPush->flush() as $report) {
                if ($report->isSuccess()) {
                    return true;
                } else {
                    // Mark subscription as error
                    $subscription->markAsError();
                    return false;
                }
            }

            return true;
        } catch (\Exception $e) {
            $subscription->markAsError();
            return false;
        }
    }

    /**
     * Generate VAPID keys.
     *
     * @return array
     */
    public static function generateVapidKeys(): array
    {
        return WebPush::generateVapidKeys();
    }
}
