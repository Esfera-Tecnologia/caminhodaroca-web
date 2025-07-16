<?php

namespace App\Http\Controllers;

use App\Models\AccessProfile;
use App\Models\User;
use App\Models\Category;
use App\Models\Subcategory;
use App\Models\Property;

class DashboardController extends Controller
{
    public function index()
    {
        return view('dashboard', [
            'perfisCount' => AccessProfile::count(),
            'usuariosCount' => User::count(),
            'categoriasCount' => Category::count(),
            'subcategoriasCount' => Subcategory::count(),
            'propriedadesCount' => Property::count(),
        ]);
    }
   
}
