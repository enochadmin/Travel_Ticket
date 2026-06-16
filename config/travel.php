<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Commercial Director Email
    |--------------------------------------------------------------------------
    |
    | Used as the default mailbox for Microsoft 365 SMTP and as a fallback
    | recipient when sending commercial-director ticket notifications by email.
    |
    */
    'commercial_director_email' => env('COMMERCIAL_DIRECTOR_EMAIL', 'Henokp@eeigconstruction.com'),
];
