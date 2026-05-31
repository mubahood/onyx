<?php
// Minimal bootstrap — just enough to test URL generation
$_SERVER['HTTP_HOST']   = 'localhost:8888';
$_SERVER['REQUEST_URI'] = '/onyx/test-url.php';
$_SERVER['SCRIPT_NAME'] = '/onyx/test-url.php';
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$request = Illuminate\Http\Request::capture();
$app->instance('request', $request);
$app->make(Illuminate\Contracts\Http\Kernel::class);
$provider = new App\Providers\AppServiceProvider($app);
$provider->boot();
echo json_encode([
    'app_url'    => config('app.url'),
    'url_helper' => url('/admin/api'),
    'route'      => route('admin.api.cases.show', 1),
]);
@unlink(__FILE__);
