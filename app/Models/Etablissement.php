<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Etablissement extends Model
{
    use HasFactory;
    protected $fillable = [
        'id',
        'idZap',
        'code',
        'nomEtab',
        'telephone'
    ];

    public function zap()
    {
        return $this->belongsTo(Zap::class, 'idZap');
    }
    
    public function distributions()
    {
        return $this->hasMany(Distribution::class, 'idEtab');
    }
    
}
