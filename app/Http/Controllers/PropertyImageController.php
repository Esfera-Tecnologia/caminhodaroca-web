<?php

namespace App\Http\Controllers;
use App\Models\PreapprovedPropertyImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\PropertyImage;

class PropertyImageController extends Controller
{

    public function remover(Request $request)
    {
        $id = $request->input('id');

        if($request->type == 'property')
            $image = PropertyImage::find($id);
        elseif ($request->type == 'preapproved_property')
            $image = PreapprovedPropertyImage::find($id);
        else
            return response()->json(['error' => 'Imagem não encontrada'], 404);


        if (!$image) {
            return response()->json(['error' => 'Imagem não encontrada'], 404);
        }

        // Remove do disco
        if (Storage::disk('public')->exists($image->path) && $request->type != 'preapproved_property') {
            Storage::disk('public')->delete($image->path);
        }

        $preapprovedImage = PreapprovedPropertyImage::where('path', $image->path)->first();
        if($preapprovedImage){
            $preapprovedImage->delete();
        }
        // Remove do banco
        $image->delete();

        return response()->json(['success' => true]);
    }
}
