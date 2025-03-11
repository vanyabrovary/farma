<?php
namespace app\models\elastic;

use yii\elasticsearch\ActiveRecord;

class InvoiceElastic extends ActiveRecord
{
    use IndexTrait;

    public static function index()
    {
        return 'farma_invoice';
    }

    public function attributes()
    {
        return [
            'city',
            'region',
            'product_name',
            'product_quantity',
            'invoice_date',
            'row_checksum'
        ];
    }

    public static function mapping()
    {
        return [
            'properties' => [
                'city' => ['type' => 'keyword'],
                'region' => ['type' => 'keyword'],
                'product_name' => ['type' => 'keyword'],
                'product_quantity' => ['type' => 'integer'],
                'invoice_date' => ['type' => 'text', 'fields' => ['keyword' => ['type' => 'keyword']]],
                'row_checksum' => ['type' => 'text', 'fields' => ['keyword' => ['type' => 'keyword']]],
            ]
        ];
    }

}
