<?php

namespace App\Models\Sire;

use Illuminate\Database\Eloquent\Model;
use App\Models\Sire\SireCompras;

class SireComprasItem extends Model
{
    protected $table = 'sire_compras_items';

    protected $fillable = [
        'compra_id',
        'invoicedQuantity',
        'unitCode',
        'priceAmount',
        'priceTypeCode',
        'sellersId',
        'description',
        'itemClassCode',
        'taxAmount',
        'taxableAmount',
        'taxExemptionReasonCode',
        'percent',
    ];

    /**
     * Get the compra that owns the item.
     */
    public function compra()
    {
        return $this->belongsTo(SireCompras::class, 'compra_id');
    }
}
