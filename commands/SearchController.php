<?php

namespace app\commands;

use yii\console\{Controller,ExitCode};
use app\models\elastic\InvoiceElastic;

/**
 * Display invoices data from Elastic
 *
 */
class SearchController extends Controller
{

    /**
     * Display aggregated invoices data from Elastic
     *
     * @return int
     */
    public function actionInvoice()
    {
        $response = InvoiceElastic::find()
            ->addAggregate(
                'regions', [
                    'terms' => [
                        'field' => 'region',
                        "size" => 100
                    ],
                    "aggs" => [
                        "products" => [
                            "terms" => [
                                "field" => "product_name",
                                "size" => 10000
                            ],
                            "aggs" =>[
                                "total_quantity" => [
                                    "sum" =>[
                                        "field" => "product_quantity"
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            )->limit(0)->search();

        if (!empty($response['aggregations']['regions']['buckets'])) {
            foreach ($response['aggregations']['regions']['buckets'] as $region) {
                echo "Регион: " . $region['key'] . "\n";

                if (!empty($region['products']['buckets'])) {
                    foreach ($region['products']['buckets'] as $product) {
                        echo "  Продукт: " . $product['key'] . " - Количество: " . $product['total_quantity']['value'] . "\n";

                    }
                }
                echo "------------------------\n";
            }
        } else {
            echo "No data.";
        }

        return ExitCode::OK;
    }
}
