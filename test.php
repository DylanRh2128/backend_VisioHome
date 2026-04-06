<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    $prop = \App\Models\Propiedad::first();
    $request = Illuminate\Http\Request::create('/api/user/favorites/toggle/' . $prop->idPropiedad, 'POST');
    $user = \App\Models\Usuario::first();
    $request->setUserResolver(function() use ($user) { return $user; });
    $controller = new \App\Http\Controllers\Api\FavoriteController();
    $response = $controller->toggle($request, 1);
    echo $response->getContent();
} catch (\Exception $e) {
    echo "ERROR DETECTADO: " . $e->getMessage() . "\nFILE: " . $e->getFile() . "\nLINE: " . $e->getLine();
}
