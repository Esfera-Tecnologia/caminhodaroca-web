<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PartnerCategory;
use Illuminate\Http\JsonResponse;

class PartnerCategoryController extends Controller
{
    public function index(): JsonResponse
    {
        $categories = PartnerCategory::select('id as value', 'titulo as label', 'experiencias_oferecidas')
            ->where('status', 'ativo')
            ->orderBy('titulo')
            ->get();

        return response()->json($categories);
    }
}
