<?php

namespace App\Http\Controllers;

use App\Enums\StatusPreapprovedProperty;
use App\Enums\StatusProperty;
use App\Models\AccessProfile;
use App\Models\Category;
use App\Models\Menu;
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

        $properties = Property::query()->when(Auth::user()->isResponsible(), function ($q) {
            $q->where('email_responsavel', Auth::user()->email);
        })->latest()->get();
        return view('properties.index', compact('properties'));
    }

    public function create()
    {
        $permissao = $this->getPermissao('properties');
        abort_unless($permissao?->can_create, 403);

        $categories = Category::with('subcategories')->where('status', 'ativo')->get();
        $products = Product::where('status', 'ativo')->get();
        return view('properties.create', compact('categories', 'products'));
    }

    public function store(Request $request)
    {
        $permissao = $this->getPermissao('properties');
        abort_unless($permissao?->can_create, 403);

        $this->storeProperty($request);

        return redirect()->route('properties.index')->with('success', 'Propriedade cadastrada com sucesso!');
    }

    public function storeProperty(Request $request)
    {
        $data = $this->validateData($request);

        // Upload do logo
        if ($request->hasFile('logo')) {
            $data['logo_path'] = $request->file('logo')->store('logos', 'public');
        }
        $data['instagram'] = '@' . ltrim($data['instagram'], '@');
        $data['agenda_personalizada'] = $request->agenda_personalizada ?? [];

        if (!User::query()->where('email', $request->input('email_responsavel'))->exists()) {
            $user = User::query()->create([
                'name' => $request->input('nome_responsavel'),
                'email' => $request->input('email_responsavel'),
                'password' => bcrypt(Str::random(12)),
                'access_profile_id' => AccessProfile::where('nome', 'Responsável')->first()->id
            ]);
            $user->notify(new WelcomeNewUserNotification($user));
        }
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


        return $property;
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


        return view('properties.edit', compact('property', 'categories', 'products', 'galeria'));
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


        if (!Auth::user()->isResponsible()) {
            $property->update($data);
            $this->syncCategoriasSubcategorias($property, $request);
            $property->products()->sync($request->input('product_ids', []));
        }

        $preapproved = $property->preapproved_property()->first();
        if($preapproved) {
            $this->syncPreapprovedCategoriasSubcategorias($preapproved, $request);
            $dataProperty = $data;
            if (!Auth::user()->isResponsible()) {
                $dataProperty['status'] = StatusPreapprovedProperty::APPROVED;
            } else {
                $dataProperty['status'] = StatusPreapprovedProperty::PENDING;
            }
            $property->preapproved_property()->update($dataProperty);
            $preapproved->images()->delete();
            $preapproved->images()->createMany($property->images->toArray());
            $preapproved->products()->sync($request->input('product_ids', []));
        }

        // atualiza galeria
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                $path = $file->store('properties', 'public');

                if (!Auth::user()->isResponsible()) {
                    PropertyImage::create([
                        'property_id' => $property->id,
                        'path' => $path,
                    ]);
                }

                if ($preapproved) {
                    PreapprovedPropertyImage::create([
                        'preapproved_property_id' => $preapproved->id,
                        'path' => $path,
                    ]);
                }
            }
        }

        $updateMessage = Auth::user()->isResponsible() ? 'Atualização enviado para validação!' : 'Propriedade atualizada com sucesso!';

        return redirect()->route('properties.index')->with('success', $updateMessage);
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
            'instagram' => ['nullable'],
            'endereco_principal' => ['required'],
            'endereco_secundario' => ['nullable'],
            'nome_responsavel' => ['required'],
            'email_responsavel' => ['required', 'email'],
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
            'status' => ['required', 'string', 'in:ativo,inativo'],
            'logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,gif', 'max:2048'],
//            'galeria.*' => ['nullable', 'image'],
//            'product_ids' => 'array',
//            'product_ids.*' => 'exists:products,id',
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


    protected function syncPreapprovedCategoriasSubcategorias(PreapprovedProperty $property, Request $request)
    {
        $registros = [];

        if ($request->has('categoria_ids')) {
            foreach ($request->categoria_ids as $categoriaId => $subcategorias) {
                if (count($subcategorias) === 1 && $subcategorias[0] === '') {
                    // caso especial: uma entrada vazia => subcategoria null
                    $registros[] = [
                        'preapproved_property_id' => $property->id,
                        'category_id' => $categoriaId,
                        'subcategory_id' => null,
                    ];
                } else {
                    foreach ($subcategorias as $subcategoriaId) {
                        $registros[] = [
                            'preapproved_property_id' => $property->id,
                            'category_id' => $categoriaId,
                            'subcategory_id' => $subcategoriaId ?: null,
                        ];
                    }
                }
            }
        }


        // Remove antigos e insere novos
        DB::table('category_preapproved_property_subcategories')->where('preapproved_property_id', $property->id)->delete();

        if (!empty($registros)) {
            DB::table('category_preapproved_property_subcategories')->insert($registros);
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

        $pdf->setOption('isRemoteEnabled', true);

        return $pdf->stream("property_{$property->id}.pdf");
    }

    public function testePdf(Property $property)
    {
        $categorias = $property->categorias()->where('status', 'ativo')->get();
        $categoria_principal = $property->categorias()->where('status', 'ativo')->first();
        $subcategorias_principais = $property->subcategorias()
            ->where('subcategories.category_id', $categoria_principal->id)
            ->pluck('nome')
            ->toArray();
        return view('pdf.property', compact('property', 'categorias', 'categoria_principal', 'subcategorias_principais'));
    }


    public function create_public()
    {
        $categories = Category::with('subcategories')->where('status', 'ativo')->get();
        $products = Product::where('status', 'ativo')->get();
        return view('properties.public_create', compact('categories', 'products'));
    }

    public function store_public(Request $request)
    {
        $data = $this->validateData($request);

        // Upload do logo
        if ($request->hasFile('logo')) {
            $data['logo_path'] = $request->file('logo')->store('logos', 'public');
        }


        $data['instagram'] = '@' . ltrim($data['instagram'], '@');
        $data['agenda_personalizada'] = $request->agenda_personalizada ?? [];
        $request->status = StatusProperty::INATIVO;
        $property = $this->storeProperty($request);
        $data['status'] = StatusPreapprovedProperty::PENDING;
        $data['property_id'] = $property->id;
        $preapproved_property = PreapprovedProperty::query()->create($data);

        if (!User::query()->where('email', $request->input('email_responsavel'))->exists()) {
            $user = User::query()->create([
                'name' => $request->input('nome_responsavel'),
                'email' => $request->input('email_responsavel'),
                'password' => bcrypt(Str::random(12)),
                'access_profile_id' => AccessProfile::where('nome', 'Responsável')->first()->id
            ]);
            $user->notify(new WelcomeNewUserNotification($user));
        }

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                $path = $file->store('properties', 'public');

                PreapprovedPropertyImage::query()->create([
                    'preapproved_property_id' => $preapproved_property->id,
                    'path' => $path,
                ]);
            }
        }

        // Relacionamento categoria/subcategoria
        $this->syncPreapprovedCategoriasSubcategorias($preapproved_property, $request);
        // Relacionamento produto
        $preapproved_property->products()->sync($request->input('product_ids', []));


        return redirect()->route('properties.public.create')->with('success', 'Cadastro enviado para validação!');
    }

    public function edit_public(PreapprovedProperty $property)
    {
        $permissao = $this->getPermissao('properties');
        abort_unless($permissao?->can_edit, 403);

        $categories = Category::with('subcategories')->where('status', 'ativo')->get();
        $property->load('categorias', 'subcategorias', 'products');
        $products = Product::where('status', 'ativo')->get();
        $selectedProducts = $property->products()->pluck('product_id')->toArray();

        $galeria = collect($property->galeria_paths)->map(fn($path) => asset('storage/' . $path));


        return view('properties.edit', compact('property', 'categories', 'products', 'galeria'));
    }

    public function update_public(Request $request, PreapprovedProperty $property)
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

        // atualiza galeria
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                $path = $file->store('properties', 'public');

                PreapprovedPropertyImage::create([
                    'preapproved_property_id' => $property->id,
                    'path' => $path,
                ]);
            }
        }

        $this->syncPreapprovedCategoriasSubcategorias($property, $request);
        $property->products()->sync($request->input('product_ids', []));

        if ($request->has('approve_updates') && $request->input('approve_updates')  || ($property->status != $request->input('status'))) {
            $data['status'] = StatusPreapprovedProperty::APPROVED;
            $dataProperty = $data;
            $dataProperty['approved'] = 1;
            $dataProperty['status'] = StatusProperty::ATIVO;
            $property->property()->update($dataProperty);
            $property->property()->first()->images()->delete();
            $property->property()->first()->images()->createMany($property->images->toArray());
            $this->syncCategoriasSubcategorias($property->property, $request);
            $property->property()->first()->products()->sync($request->input('product_ids', []));
        }

        $property->update($data);

        return redirect()->route('properties.index')->with('success', 'Atualização enviado para validação!');
    }

}
