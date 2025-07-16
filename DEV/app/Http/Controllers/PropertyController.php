<?php

namespace App\Http\Controllers;

use App\Models\Property;
use App\Models\Category;
use App\Models\Menu;
use App\Models\Subcategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class PropertyController extends Controller
{
     private function getPermissao(string $slug)
    {
        $menuId = Menu::where('slug', $slug)->value('id');

        return auth()->user()
            ->accessProfile
            ->permissions
            ->firstWhere('menu_id', $menuId);
    }

    public function index()
    {

         $permissao = $this->getPermissao('properties');
        abort_unless($permissao?->can_view, 403);

        $properties = Property::latest()->get();
        return view('properties.index', compact('properties'));
    }

    public function create()
    {
        $permissao = $this->getPermissao('properties');
        abort_unless($permissao?->can_create, 403);

        $categories = Category::with('subcategories')->where('status', 'ativo')->get();
        return view('properties.create', compact('categories'));
    }

    public function store(Request $request)
    {
        
        $permissao = $this->getPermissao('properties');
        abort_unless($permissao?->can_create, 403);
        
        $data = $this->validateData($request);

        // Upload do logo
        if ($request->hasFile('logo')) {
            $data['logo_path'] = $request->file('logo')->store('logos', 'public');
        }

        // Upload da galeria
        $galeriaPaths = [];
        if ($request->hasFile('galeria')) {
            foreach ($request->file('galeria') as $image) {
                $galeriaPaths[] = $image->store('galeria', 'public');
            }
        }
        $data['galeria_paths'] = $galeriaPaths;

        $data['instagram'] = '@' . ltrim($data['instagram'], '@');
        $data['agenda_personalizada'] = $request->agenda_personalizada ?? [];
        $property = Property::create($data);

        // Relacionamento categoria/subcategoria
        $this->syncCategoriasSubcategorias($property, $request);

        return redirect()->route('properties.index')->with('success', 'Propriedade cadastrada com sucesso!');
    }

    public function edit(Property $property)
    {
        $permissao = $this->getPermissao('properties');
        abort_unless($permissao?->can_edit, 403);
       
        $categories = Category::with('subcategories')->where('status', 'ativo')->get();
        $property->load('categorias', 'subcategorias');
 
        return view('properties.edit', compact('property', 'categories'));
    }

    public function update(Request $request, Property $property)
    {

        $data = $this->validateData($request, $property->id);

        if ($request->hasFile('logo')) {
            // Remove logo antigo
            if ($property->logo_path) {
                Storage::disk('public')->delete($property->logo_path);
            }
            $data['logo_path'] = $request->file('logo')->store('logos', 'public');
        }

        if ($request->hasFile('galeria')) {
            // Remove imagens antigas
            if ($property->galeria_paths) {
                foreach ($property->galeria_paths as $img) {
                    Storage::disk('public')->delete($img);
                }
            }
            $galeriaPaths = [];
            foreach ($request->file('galeria') as $image) {
                $galeriaPaths[] = $image->store('galeria', 'public');
            }
            $data['galeria_paths'] = $galeriaPaths;
        }

        $data['instagram'] = '@' . ltrim($data['instagram'], '@');
        $data['agenda_personalizada'] = $request->agenda_personalizada ?? [];


        $property->update($data);

        $this->syncCategoriasSubcategorias($property, $request);

        return redirect()->route('properties.index')->with('success', 'Propriedade atualizada com sucesso!');
    }

    public function destroy(Property $property)
    {
        $permissao = $this->getPermissao('properties');
        abort_unless($permissao?->can_delete, 403);
        // Remover imagens
        if ($property->logo_path) {
            Storage::disk('public')->delete($property->logo_path);
        }
        if ($property->galeria_paths) {
            foreach ($property->galeria_paths as $img) {
                Storage::disk('public')->delete($img);
            }
        }

        $property->delete();
        return redirect()->route('properties.index')->with('success', 'Propriedade excluída com sucesso!');
    }

    protected function validateData(Request $request, $id = null)
    {
        return $request->validate([
            'name' => ['required', 'string'],
            'whatsapp' => ['required'],
            'status' => ['required', Rule::in(['ativo', 'inativo'])],
            'instagram' => ['nullable'],
            'endereco_principal' => ['required'],
            'endereco_secundario' => ['nullable'],
            'cidade' => ['required'],
            'descricao_servico' => ['required', 'max:1000'],
            'certificacao' => ['nullable', Rule::in([0, 1, 2])],
            'vende_produtos_artesanais' => ['boolean'],
            'produtos_artesanais' => ['nullable', 'array'],
            'tipo_funcionamento' => ['required', Rule::in(['todos', 'fins', 'feriados', 'agendamento', 'personalizado'])],
            'observacoes_funcionamento' => ['nullable', 'string'],
            'agenda_personalizada' => ['nullable', 'array'],
            'agenda_personalizada.*.abertura' => ['nullable', 'date_format:H:i'],
            'agenda_personalizada.*.fechamento' => ['nullable', 'date_format:H:i'],
            'agenda_personalizada.*.fechar_almoco' => ['nullable', 'boolean'],
            'agenda_personalizada.*.ativo' => ['nullable', 'boolean'],
            'aceita_animais' => ['boolean'],
            'possui_acessibilidade' => ['boolean'],
            'logo' => ['nullable', 'image'],
            'galeria.*' => ['nullable', 'image'],
        ]);
    }


   protected function syncCategoriasSubcategorias(Property $property, Request $request)
    {
        $registros = [];

        if ($request->has('categoria_ids')) {
            foreach ($request->categoria_ids as $categoriaId => $subcategorias) {
                if (count($subcategorias) === 1 && $subcategorias[0] === '') {
                    // caso especial: uma entrada vazia => subcategoria null
                    $registros[] = [
                        'property_id' => $property->id,
                        'category_id' => $categoriaId,
                        'subcategory_id' => null,
                    ];
                } else {
                    foreach ($subcategorias as $subcategoriaId) {
                        $registros[] = [
                            'property_id' => $property->id,
                            'category_id' => $categoriaId,
                            'subcategory_id' => $subcategoriaId ?: null,
                        ];
                    }
                }
            }
        }

        // Remove antigos e insere novos
        \DB::table('category_property_subcategories')->where('property_id', $property->id)->delete();

        if (!empty($registros)) {
            \DB::table('category_property_subcategories')->insert($registros);
        }
    }

}
