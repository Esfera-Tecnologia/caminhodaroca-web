<?php

use App\Models\User;
use App\Models\Property;
use App\Http\Controllers\Api\FavoriteListController;
use App\Http\Controllers\Api\PropertyController;
use Illuminate\Http\Request;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$user = User::find(1);
Auth::login($user);

$listController = new FavoriteListController();
$propController = new PropertyController();

echo "--- 1. LISTING FAVORITE LISTS ---\n";
$response = $listController->index();
echo json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n\n";

echo "--- 2. CREATING NEW LIST 'Viagem de Férias' ---\n";
$request = Request::create('/api/favorite-lists', 'POST', ['name' => 'Viagem de Férias']);
$request->setUserResolver(fn() => $user); // Autentica o request
$response = $listController->store($request);
$newList = $response->resource;
echo json_encode($newList, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n\n";

echo "--- 3. FAVORITING PROPERTY 10 IN BOTH LISTS ---\n";
$defaultList = $user->favoriteLists()->where('is_default', true)->first();
$request = Request::create('/api/properties/10/favorite', 'POST', [
    'list_ids' => [$defaultList->id, $newList->id]
]);
$request->setUserResolver(fn() => $user); // Autentica o request
$response = $propController->toggleFavorite(10, $request);
echo json_encode($response->getData(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n\n";

echo "--- 4. PROPERTY DETAILS WITH FAVORITE INFO ---\n";
$request = Request::create("/api/properties/10", "GET");
$request->setUserResolver(fn() => $user);
$response = $propController->show(10, $request);
$data = $response->getData();
echo "isFavorited: " . ($data->isFavorited ? 'true' : 'false') . "\n";
echo "favorite_count: " . $data->favorite_count . "\n";
echo "favorite_list_ids: " . json_encode($data->favorite_list_ids) . "\n\n";

echo "--- 5. DELETING THE NEW LIST ---\n";
$response = $listController->destroy($newList->id);
echo json_encode($response->getData(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n\n";
