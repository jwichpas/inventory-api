<?php

namespace App\Models\Sire;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Sire\SireComprasDespatchAdviceDetail;

class SireComprasDespatchAdvice extends Model
{
    use HasFactory;

    protected $table = 'sire_compras_despatch_advice';
    protected $primaryKey = 'id_compra';
    public $incrementing = true;

    protected $fillable = [
        'id_compra',
        'DespatchAdviceTypeCode',
        'numero_guia',
        'IssueDate',
        'IssueTime',
        'HandlingCode',
        'HandlingInstructions',
        'unitCode',
        'GrossWeightMeasure',
        'TransportModeCode',
        'StartDate',
        'DespatchSupplierPartyId',
        'DespatchSupplierPartyName',
        'DeliveryCustomerPartyId',
        'DeliveryCustomerPartyName',
        'CarrierPartyId',
        'CarrierPartyName',
        'DeliveryAddressId',
        'DeliveryAddressLine',
        'DespatchAddressId',
        'DespatchAddressLine',
        'xml_file',
        'pdf_file'
    ];

    protected $casts = [
        'IssueDate' => 'date',
        'StartDate' => 'date',
        'GrossWeightMeasure' => 'decimal:2',
    ];

    public function details()
    {
        return $this->hasMany(SireComprasDespatchAdviceDetail::class, 'id_compra');
    }
}
