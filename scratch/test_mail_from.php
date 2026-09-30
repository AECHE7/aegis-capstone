<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    Illuminate\Support\Facades\Mail::raw('Test email from Aegis', function($m) {
        $m->from('hello@example.com', 'Aegis Test')
          ->to('gadianoriel07@gmail.com')
          ->subject('Aegis Mail Test');
    });
    echo "SUCCESS\n";
} catch (\Throwable $e) {
    echo "FAIL: " . $e->getMessage() . "\n";
}
