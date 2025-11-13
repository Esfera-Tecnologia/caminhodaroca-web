<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PartnerResource;
use App\Models\Partner;
use Illuminate\Http\Request;

class PartnerController extends Controller
{
    public function index(Request $request)
    {
        try {
            $partners = PartnerResource::collection(Partner::query()
                ->city($request->city??[])
                ->category($request->categories??[])
                ->subcategory($request->subcategories??[])
                ->keyword($request->keyword??null)
                ->get());
            return response()->json($partners);
        }catch (\Exception $exception){
            dd($exception);
            return response()->json(['status'=>false,'message'=>'Não foi possível buscar as informações'], 500);
        }
    }

    public function show(Partner $id)
    {
        $id->individual = true;

        $partner = PartnerResource::make($id);

        return response()->json($partner);
    }
}
