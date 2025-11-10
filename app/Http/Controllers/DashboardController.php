<?php

namespace App\Http\Controllers;

use App\Models\AccessProfile;
use App\Models\User;
use App\Models\Category;
use App\Models\Subcategory;
use App\Models\Property;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        if(Auth::user()->isResponsible()){
            return redirect()->route('properties.index');
        }

        return view('dashboard', [
            'perfisCount' => AccessProfile::count(),
            'usuariosCount' => User::where('registration_source', 'web')->count(),
            'categoriasCount' => Category::count(),
            'subcategoriasCount' => Subcategory::count(),
            'propriedadesCount' => Property::count(),
        ]);
    }

}
