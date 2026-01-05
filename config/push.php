<?php

return [
    'public_key' => env('PUSH_PUBLIC_KEY'),
    'private_key' => env('PUSH_PRIVATE_KEY'),
    'subject' => env('PUSH_SUBJECT', 'mailto:' . env('MAIL_FROM_ADDRESS', 'app@daymeter.local')),
];
