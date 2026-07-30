<?php

namespace App\Http\Controllers;

use App\Models\PartnerCategory;
use Illuminate\Http\Request;

class PartnerCategoryController extends Controller
{
    public function index()
    {
        $permissao = getPermissao('partner-categories');
        abort_unless($permissao?->can_view, 403);

        $categories = PartnerCategory::orderBy('titulo')->get();
        return view('partner-categories.index', compact('categories'));
    }

    public function create()
    {
        $permissao = getPermissao('partner-categories');
        abort_unless($permissao?->can_create, 403);

        $category = new PartnerCategory();
        return view('partner-categories.create', compact('category'));
    }

    public function store(Request $request)
    {
        $permissao = getPermissao('partner-categories');
        abort_unless($permissao?->can_create, 403);

        $validated = $request->validate([
            'titulo'                     => 'required|string|max:191|unique:partner_categories,titulo',
            'status'                     => 'required|in:ativo,inativo',
            'experiencias_oferecidas'    => 'required|boolean',
        ]);

        PartnerCategory::create($validated);

        return redirect()->route('partner-categories.index')
            ->with('success', 'Categoria de parceiro cadastrada com sucesso.');
    }

    public function edit(PartnerCategory $partnerCategory)
    {
        $permissao = getPermissao('partner-categories');
        abort_unless($permissao?->can_edit, 403);

        return view('partner-categories.edit', compact('partnerCategory'));
    }

    public function update(Request $request, PartnerCategory $partnerCategory)
    {
        $permissao = getPermissao('partner-categories');
        abort_unless($permissao?->can_edit, 403);

        $validated = $request->validate([
            'titulo'                     => 'required|string|max:191|unique:partner_categories,titulo,' . $partnerCategory->id,
            'status'                     => 'required|in:ativo,inativo',
            'experiencias_oferecidas'    => 'required|boolean',
        ]);

        $partnerCategory->update($validated);

        return redirect()->route('partner-categories.index')
            ->with('success', 'Categoria de parceiro atualizada com sucesso.');
    }

    public function destroy(PartnerCategory $partnerCategory)
    {
        $permissao = getPermissao('partner-categories');
        abort_unless($permissao?->can_delete, 403);

        $partnerCategory->delete();

        return redirect()->route('partner-categories.index')
            ->with('success', 'Categoria de parceiro excluída com sucesso.');
    }
}
