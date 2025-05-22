<?php

namespace App\Models\Sire;

use Illuminate\Database\Eloquent\Model;
use App\Models\Empresa;

class SireResumen extends Model
{
    protected $table = 'sire_resumens';
    protected $primaryKey = 'id';
    protected $fillable = [
        'numRuc',
        'nomRazonSocial',
        'perTributario',
        'cntRegistrosPresentadosDP',
        'cntRegistrosPresentadosFP',
        'cntRegistrosPresentadosNG',
        'cntRegistrosPresentados'
    ];

    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'numRuc', 'ruc');
    }

}
