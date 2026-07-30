<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PartnerCategory extends Model
{
    protected $table = 'partner_categories';

    protected $fillable = [
        'titulo',
        'status',
        'experiencias_oferecidas',
    ];

    protected $casts = [
        'experiencias_oferecidas' => 'boolean',
    ];

    public function scopeActive($query)
    {
        return $query->where('status', 'ativo');
    }
}
