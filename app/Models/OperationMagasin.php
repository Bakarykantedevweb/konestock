<?php

namespace App\Models;

use App\Models\Magasin;
use App\Models\Produit;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OperationMagasin extends Model
{
    use HasFactory;

    protected $table = 'operation_magasins';

    protected $guarded = [];

    public function produit()
    {
        return $this->belongsTo(Produit::class);
    }

    public function magasin()
    {
        return $this->belongsTo(Magasin::class);
    }
}
