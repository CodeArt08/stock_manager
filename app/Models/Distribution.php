<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Distribution extends Model
{
    use HasFactory;
    protected $fillable = [
        'idStock',
        'idZap',
        'idEtab',
        'nbrCarton',
        'nbrPiece',
        'dateDist'
    ];

    public function zap()
    {
        return $this->belongsTo(Zap::class, 'idZap');
    }
    
    public function stock()
    {
        return $this->belongsTo(Stock::class, 'idStock');
    }
    
    public function etablissement()
    {
        return $this->belongsTo(Etablissement::class, 'idEtab');
    }
    
    
    
    
}
