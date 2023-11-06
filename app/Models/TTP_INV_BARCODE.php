<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TTP_INV_BARCODE extends Model
{
    protected $table = 'TTP_INV_BARCODE';

    public $timestamps = false;

    use HasFactory;

    protected $primaryKey = 'SEQ_ID';

    protected $fillable = [
        'SEQ_ID',
        'INV_NO',
        'INV_DATE',
        'DUE_DATE',
        'REF1_NO',
        'REF2_NO',
        'INV_AMT',
        'CUST_CARD_ID',
        'BARCODE_FILE',
        'QRCODE_FILE',
        'CRT_FLG',
        'UPDATE_DATE',
        'UPDATE_BY',
    ];
}
