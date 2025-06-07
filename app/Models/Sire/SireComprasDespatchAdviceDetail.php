<?php

namespace App\Models\Sire;

use Illuminate\Database\Eloquent\Model;
use App\Models\Sire\SireComprasDespatchAdvice;

class SireComprasDespatchAdviceDetail extends Model
{
    protected $table = 'sire_compras_despatch_advice_details';

    protected $fillable = [
        'id_compra',
        'ItemIdentification',
        'ItemDescription',
        'DespatchLine',
        'DeliveredQuantity',
        'unitCode',
        'GrossWeightMeasure'
    ];

    protected $casts = [
        'DeliveredQuantity' => 'decimal:2',
        'GrossWeightMeasure' => 'decimal:2',
    ];

    public function despatchAdvice()
    {
        return $this->belongsTo(SireComprasDespatchAdvice::class, 'id');
    }
}
