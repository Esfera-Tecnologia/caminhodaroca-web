<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{
    public function index()
    {
        $permissao = $this->getPermissao('products');
        abort_unless($permissao?->can_view, 403);

        $products = Product::orderBy('nome')->get();
        return view('products.index', compact('products'));
    }

    public function create()
    {
        $permissao = $this->getPermissao('products');
        abort_unless($permissao?->can_create, 403);

        $products = new Product();
        return view('products.create', compact('products'));
    }

    public function store(Request $request)
    {
        $permissao = $this->getPermissao('products');
        abort_unless($permissao?->can_create, 403);

        $validated = $request->validate([
            'nome' => 'required|string|max:191|unique:products,nome',
            'status' => 'required|in:ativo,inativo',
        ]);

        Product::create($validated);

        return redirect()->route('products.index')->with('success', 'Produto cadastrado com sucesso.');
    }

    public function edit(Product $product)
    {
        $permissao = $this->getPermissao('products');
        abort_unless($permissao?->can_edit, 403);

        return view('products.edit', compact('product'));
    }

    public function update(Request $request, Product $product)
    {
       

        $validated = $request->validate([
            'nome' => 'required|string|max:191|unique:products,nome,' . $product->id,
            'status' => 'required|in:ativo,inativo',
        ]);

        $product->update($validated);

        return redirect()->route('products.index')->with('success', 'Produto atualizado com sucesso.');
    }

    public function destroy(Product $product)
    {
        $permissao = $this->getPermissao('products');
        abort_unless($permissao?->can_delete, 403);

        if ($product->properties()->exists()) {
            return redirect()->back()->with('error', 'Não é possível excluir um produto vinculado a uma ou mais propriedades.');
        }

        $product->delete();

        return redirect()->route('products.index')->with('success', 'Produto excluído com sucesso.');
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
