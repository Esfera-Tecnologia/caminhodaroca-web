<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Menu;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $permissao = $this->getPermissao('categories');
        abort_unless($permissao?->can_view, 403);

        $categories = Category::orderBy('nome')->get();
        return view('categories.index', compact('categories'));
    }

    public function create()
    {
        $permissao = $this->getPermissao('categories');
        abort_unless($permissao?->can_create, 403);

        $category = new Category();
        return view('categories.create', compact('category'));
    }

    public function store(Request $request)
    {
        $permissao = $this->getPermissao('categories');
        abort_unless($permissao?->can_create, 403);

        $validated = $request->validate([
            'nome' => 'required|string|max:191|unique:categories,nome',
            'descricao' => 'nullable|string|max:255',
            'status' => 'required|in:ativo,inativo',
        ]);

        Category::create($validated);

        return redirect()->route('categories.index')->with('success', 'Categoria cadastrada com sucesso.');
    }

    public function edit(Category $category)
    {
        $permissao = $this->getPermissao('categories');
        abort_unless($permissao?->can_edit, 403);

        return view('categories.edit', compact('category'));
    }

    public function update(Request $request, Category $category)
    {
       

        $validated = $request->validate([
            'nome' => 'required|string|max:191|unique:categories,nome,' . $category->id,
            'descricao' => 'nullable|string|max:255',
            'status' => 'required|in:ativo,inativo',
        ]);

        $category->update($validated);

        return redirect()->route('categories.index')->with('success', 'Categoria atualizada com sucesso.');
    }

    public function destroy(Category $category)
    {
        $permissao = $this->getPermissao('categories');
        abort_unless($permissao?->can_delete, 403);

        if ($category->subcategories()->exists()) {
            return back()->with('error', 'Não é possível excluir esta categoria. Existem subcategorias vinculadas a ela.');
        }

        $category->delete();

        return redirect()->route('categories.index')->with('success', 'Categoria excluída com sucesso.');
    }

    private function getPermissao(string $slug)
    {
        $menuId = Menu::where('slug', $slug)->value('id');

        return auth()->user()
            ->accessProfile
            ->permissions
            ->firstWhere('menu_id', $menuId);
    }

    public function storeAjax(Request $request)
    {
        $validated = $request->validate([
            'nome' => 'required|string|max:191|unique:categories,nome',
        ]);

        $categoria = Category::create([
            'nome' => $validated['nome'],
            'status' => 'ativo',
        ]);

        return response()->json([
            'success' => true,
            'id' => $categoria->id,
            'nome' => $categoria->nome,
        ]);
    }

}
