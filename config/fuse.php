<?php

return [
    'admin_emails' => env('ADMIN_EMAILS') ? explode(',', env('ADMIN_EMAILS')) : [],
];
