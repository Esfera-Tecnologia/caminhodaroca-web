<?php

namespace App\Http\Controllers;

use App\Models\PropertyImage;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;

class PropertyImageController extends Controller
{
   public function destroy($id)
{
    $image = PropertyImage::findOrFail($id);

    // Apagar o arquivo
    if (Storage::disk('public')->exists($image->path)) {
        Storage::disk('public')->delete($image->path);
    }

    // Remover do banco
    $image->delete();

    return response()->json(['success' => true]);
}
}