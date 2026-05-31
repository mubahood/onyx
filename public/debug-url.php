<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Http\Kernel::class);
$app->boot();
echo 'APP_URL: ' . config('app.url') . "\n";
echo 'url(): ' . url('/admin/api') . "\n";
echo 'asset(): ' . asset('js/admin.js') . "\n";
@unlink(__FILE__);
