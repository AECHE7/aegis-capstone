<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    Illuminate\Support\Facades\Mail::mailer('smtp')->raw('Test email from Aegis via SMTP', function($m) {
        $m->to('gadianoriel07@gmail.com')
          ->subject('Aegis Mail Test SMTP');
    });
    echo "SUCCESS\n";
} catch (\Throwable $e) {
    echo "FAIL: " . $e->getMessage() . "\n";
}
