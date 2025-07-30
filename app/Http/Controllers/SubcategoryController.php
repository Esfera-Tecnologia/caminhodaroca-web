<?php

namespace App\Http\Controllers;

use App\Models\Subcategory;
use App\Models\Category;
use App\Models\Menu;
use Illuminate\Http\Request;

class SubcategoryController extends Controller
{
    public function index()
    {
        $permissao = $this->getPermissao('subcategories');
        abort_unless($permissao?->can_view, 403);

        $subcategories = Subcategory::with('category')->orderBy('nome')->get();
        return view('subcategories.index', compact('subcategories'));
    }

    public function create()
    {
        $permissao = $this->getPermissao('subcategories');
        abort_unless($permissao?->can_create, 403);

        $subcategory = new Subcategory();
        $categories = Category::where('status', 'ativo')->orderBy('nome')->get();

        return view('subcategories.create', compact('subcategory', 'categories'));
    }

    public function store(Request $request)
    {
        $permissao = $this->getPermissao('subcategories');
        abort_unless($permissao?->can_create, 403);

        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
           'nome' => 'required|string|max:191|unique:subcategories,nome,NULL,id,category_id,' . $request->category_id,
            'status' => 'required|in:ativo,inativo',
        ]);

        Subcategory::create($validated);

        return redirect()->route('subcategories.index')->with('success', 'Subcategoria cadastrada com sucesso.');
    }

    public function edit(Subcategory $subcategory)
    {
        $permissao = $this->getPermissao('subcategories');
        abort_unless($permissao?->can_edit, 403);

        $categories = Category::where('status', 'ativo')->orderBy('nome')->get();

        return view('subcategories.edit', compact('subcategory', 'categories'));
    }

    public function update(Request $request, Subcategory $subcategory)
    {
        $permissao = $this->getPermissao('subcategories');
        abort_unless($permissao?->can_edit, 403);

        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
           'nome' => 'required|string|max:191|unique:subcategories,nome,' . $subcategory->id . ',id,category_id,' . $request->category_id,
            'status' => 'required|in:ativo,inativo',
        ]);

        $subcategory->update($validated);

        return redirect()->route('subcategories.index')->with('success', 'Subcategoria atualizada com sucesso.');
    }

    public function destroy(Subcategory $subcategory)
    {
        $permissao = $this->getPermissao('subcategories');
        abort_unless($permissao?->can_delete, 403);


        if ($subcategory->properties()->exists()) {
            return back()->with('error', 'Não é possível excluir esta subcategoria. Existem propriedades vinculadas a ela.');
        }

        $subcategory->delete();

        return redirect()->route('subcategories.index')->with('success', 'Subcategoria excluída com sucesso.');
    }

    private function getPermissao(string $slug)
    {
        $menuId = Menu::where('slug', $slug)->value('id');

        return auth()->user()
            ->accessProfile
            ->permissions
            ->firstWhere('menu_id', $menuId);
    }
}
