<?php

return [
    'email_prefix' => '[Facilities]',
    'admin_email' => env('ADMIN_EMAIL'),
    'email_alert_days' => env('EMAIL_ALERT_DAYS', 14), // number of days to send email alerts about for arrivals/departures
];
