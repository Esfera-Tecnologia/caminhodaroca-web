<?php

namespace App\Http\Controllers;

use App\Models\Property;
use App\Models\Category;
use App\Models\Menu;
use App\Models\Product;
use App\Models\PropertyImage;
use App\Models\Subcategory;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class PropertyController extends Controller
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
        $products = Product::where('status', 'ativo')->get();
        return view('properties.create', compact('categories','products'));
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


        $data['instagram'] = '@' . ltrim($data['instagram'], '@');
        $data['agenda_personalizada'] = $request->agenda_personalizada ?? [];
        $property = Property::create($data);

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                $path = $file->store('properties', 'public');

                PropertyImage::create([
                    'property_id' => $property->id,
                    'path' => $path,
                ]);
            }
        }

        // Relacionamento categoria/subcategoria
        $this->syncCategoriasSubcategorias($property, $request);
        // Relacionamento produto
       $property->products()->sync($request->input('product_ids', []));


        return redirect()->route('properties.index')->with('success', 'Propriedade cadastrada com sucesso!');
    }

    public function edit(Property $property)
    {
        $permissao = $this->getPermissao('properties');
        abort_unless($permissao?->can_edit, 403);

        $categories = Category::with('subcategories')->where('status', 'ativo')->get();
        $property->load('categorias', 'subcategorias', 'products');
        $products = Product::where('status', 'ativo')->get();
        $selectedProducts = $property->products()->pluck('product_id')->toArray();

        $galeria = collect($property->galeria_paths)->map(fn($path) => asset('storage/' . $path));



        return view('properties.edit', compact('property', 'categories', 'products','galeria'));
    }

    public function update(Request $request, Property $property)
    {

        $data = $this->validateData($request, $property->id);

        // Atualiza a logo
        if ($request->hasFile('logo')) {
            if ($property->logo_path) {
                Storage::disk('public')->delete($property->logo_path);
            }
            $data['logo_path'] = $request->file('logo')->store('logos', 'public');
        }


        $data['instagram'] = '@' . ltrim($data['instagram'], '@');
        $data['agenda_personalizada'] = $request->agenda_personalizada ?? [];


        $property->update($data);

         // atualiza galeria
       if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                $path = $file->store('properties', 'public');

                PropertyImage::create([
                    'property_id' => $property->id,
                    'path' => $path,
                ]);
            }
        }

        $this->syncCategoriasSubcategorias($property, $request);
        $property->products()->sync($request->input('product_ids', []));

        return redirect()->route('properties.index')->with('success', 'Propriedade atualizada com sucesso!');
    }

    public function deleteImage($id)
    {
        $image = PropertyImage::findOrFail($id);

        // Apaga o arquivo físico
        if (Storage::disk('public')->exists($image->path)) {
            Storage::disk('public')->delete($image->path);
        }

        // Remove do banco
        $image->delete();

        return response()->json(['message' => 'Imagem excluída com sucesso.']);
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
            'logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,gif', 'max:2048'],
            'galeria.*' => ['nullable', 'image'],
            'product_ids' => 'array',
            'product_ids.*' => 'exists:products,id',
            'google_maps_url' => ['required', 'url', 'max:2048'],
            'latitude' => ['required', 'regex:/^-?\d{1,2}\.\d+$/', 'max:15'],
            'longitude' => ['required', 'regex:/^-?\d{1,3}\.\d+$/', 'max:15'],
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
        DB::table('category_property_subcategories')->where('property_id', $property->id)->delete();

        if (!empty($registros)) {
            DB::table('category_property_subcategories')->insert($registros);
        }
    }

    public function generatePdf(Property $property)
    {
//        return view('pdf.property', compact('property'));
        $categorias = $property->categorias()->where('status', 'ativo')->get();
        $categoria_principal = $property->categorias()->where('status', 'ativo')->first();
        $subcategorias_principais = $property->subcategorias()
            ->where('subcategories.category_id', $categoria_principal->id)
            ->pluck('nome')
            ->toArray();
        $pdf = Pdf::loadView('pdf.property', compact('property', 'categorias', 'categoria_principal', 'subcategorias_principais'));

        return $pdf->stream("property_{$property->id}.pdf");
    }

}
