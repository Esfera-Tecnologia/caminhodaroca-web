<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Property extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'whatsapp',
        'status',
        'instagram',
        'endereco_principal',
        'endereco_secundario',
        'cidade',
        'descricao_servico',
        'certificacao',
        'vende_produtos_artesanais',
        'produtos_artesanais',
        'tipo_funcionamento',
        'observacoes_funcionamento',
        'agenda_personalizada',
        'aceita_animais',
        'possui_acessibilidade',
        'logo_path',
        'galeria_paths'
    ];

    protected $casts = [
        'certificacao' => 'integer',
        'vende_produtos_artesanais' => 'boolean',
        'produtos_artesanais' => 'array',
        'agenda_personalizada' => 'array',
        'galeria_paths' => 'array',
        'aceita_animais' => 'boolean',
        'possui_acessibilidade' => 'boolean',
        ];

    public function categorias()
    {
        return $this->belongsToMany(Category::class, 'category_property_subcategories')
                    ->withPivot('subcategory_id')
                    ->withTimestamps();
    }

    public function subcategorias()
    {
        return $this->belongsToMany(Subcategory::class, 'category_property_subcategories')
                    ->withPivot('category_id')
                    ->withTimestamps();
    }
}

