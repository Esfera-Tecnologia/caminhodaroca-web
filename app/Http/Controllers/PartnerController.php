<?php

namespace App\Http\Controllers;

use App\Enums\StatusPreapprovedProperty;
use App\Enums\StatusProperty;
use App\Models\AccessProfile;
use App\Models\Category;
use App\Models\Menu;
use App\Models\Partner;
use App\Models\PreapprovedProperty;
use App\Models\PreapprovedPropertyImage;
use App\Models\Product;
use App\Models\Property;
use App\Models\PropertyImage;
use App\Models\User;
use App\Notifications\WelcomeNewUserNotification;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class PartnerController extends Controller
{
    private function getPermissao(string $slug)
    {
        $menuId = Menu::where('slug', $slug)->value('id');

        return Auth::user()
            ->accessProfile
            ->permissions
            ->firstWhere('menu_id', $menuId);
    }

    public function index()
    {
        $permissao = $this->getPermissao('partners');
        abort_unless($permissao?->can_view, 403);

        $partners = Partner::query()->when(Auth::user()->isPartner(), function ($q) {
            $q->where('email', Auth::user()->email);
        })->latest()->get();
        return view('partners.index', compact('partners'));
    }

}
