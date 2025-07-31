<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\PropertyImage;

class PropertyImageController extends Controller
{

    public function remover(Request $request)
    {
        $id = $request->input('id');

        $image = PropertyImage::find($id);

        if (!$image) {
            return response()->json(['error' => 'Imagem não encontrada'], 404);
        }

        // Remove do disco
        if (Storage::disk('public')->exists($image->path)) {
            Storage::disk('public')->delete($image->path);
        }

        // Remove do banco
        $image->delete();

        return response()->json(['success' => true]);
    }
}