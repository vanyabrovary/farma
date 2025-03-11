<?php

namespace app\models\mongo;

use yii\mongodb\ActiveRecord;

class InvoiceMongo extends ActiveRecord
{
    public static function collectionName()
    {
        return 'invoice';
    }

    public function attributes() {
        return [
            '_id',
            'company',
            'region',
            'city',
            'invoice_date',
            'delivery_address',
            'client_legal_address',
            'client_name',
            'client_code',
            'client_subdivision_code',
            'client_okpo',
            'product_license',
            'product_license_exp_date',
            'product_code',
            'product_barcode',
            'product_name',
            'product_morion_code',
            'product_unit',
            'product_manufacturer',
            'product_supplier',
            'product_quantity',
            'warehouse_branch',
            'row_checksum',
        ];
    }
}
