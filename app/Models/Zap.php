<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Zap extends Model
{
    use HasFactory;
    protected $fillable = [
        'codeZap',
        'dren',
        'cisco',
        'zap',
        'nbrEtab',
    ];



public function distributions()
{
    return $this->hasMany(Distribution::class, 'idZap', 'id');
}
public function etablissements()
{
    return $this->hasMany(Etablissement::class, 'idZap'); 
}

public function updateNbrEtab()
{
    $this->nbrEtab = $this->etablissements()->count();
    $this->save();
}


}
