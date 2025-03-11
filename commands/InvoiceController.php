<?php

namespace app\commands;

use Yii;
use yii\console\{Controller,ExitCode};
use app\components\mappers\InvoiceXlsMapper;
use app\components\parsers\XlsParser;
use app\models\elastic\InvoiceElastic;
use app\models\mongo\InvoiceMongo;

/**
 * Import invoices data from xls to MongoDB and Elastic
 *
 */
class InvoiceController extends Controller
{

    /**
     * Import data from xls to MongoDB and from MongoDB to Elastic
     *
     * @return int
     */
    public function actionImport(): int
    {
        echo "\nStart import\n";

        $data = $this->getDataFomXls();
        $this->importToMongo($data);
        $this->importToElastic();

        echo "\nData was imported\n";

        return ExitCode::OK;
    }


    /**
     * Recreate Elastic indexes
     *
     * @return int
     */
    public function actionElasticRecreateMappings(): int
    {
        echo "\nRecreate Elastic indexes\n";

        InvoiceElastic::deleteIndex();
        InvoiceElastic::createIndex();

        return ExitCode::OK;
    }


    /**
     * Remove all data from MongoDB
     *
     * @return int
     */
    public function actionDeleteDataFomMongo(): int
    {
        echo "\nDelete all data from MongoDB\n";

        InvoiceMongo::deleteAll();

        return ExitCode::OK;
    }


    /**
     * Get and prepare data for MongoDB from xls file
     *
     * @return array
     */
    private function getDataFomXls(): array
    {
        echo "\nGetting data from xls\n";

        $xlsFilePath = Yii::getAlias('@runtime/tmp/file.xls');
        return (new XlsParser())->parseFile($xlsFilePath, fn($row) => InvoiceXlsMapper::map($row)) ?? [];
    }


    /**
     * Import invoice data from xls to MongoDB
     *
     * @return void
     */
    private function importToMongo(array $data): void
    {
        echo "\nImporting data to MongoDB\n";

        foreach ($data as $row) {
            if (!InvoiceMongo::find()->where(['row_checksum' => $row['row_checksum']])->exists()) {
                $invoice = new InvoiceMongo();
                $invoice->setAttributes($row, false);
                $invoice->save();
                echo "+";
            } else {
               echo ".";
            }
        }
    }


    /**
     * Import some cols from MongoDB to Elastic
     *
     * @return void
     */
    private function importToElastic(): void
    {
        echo "\nImporting data to Elastic\n";

        foreach (InvoiceMongo::find()->asArray()->all() as $row) {
            if (!InvoiceElastic::find()->where(['row_checksum' => $row['row_checksum']])->exists()) {
                $invoice = new InvoiceElastic();
                $invoice->setAttributes([
                    'city'              => $row['city'] ?? 'Unknown City',
                    'region'            => $row['region'] ?? 'Unknown Region',
                    'product_name'      => $row['product_name'] ?? 'Unknown Product',
                    'product_quantity'  => (int)($row['product_quantity'] ?? 0),
                    'invoice_date'      => $row['invoice_date'] ?? '',
                    'row_checksum'      => (string)$row['row_checksum'],
                ], false);
                $invoice->save();
                echo "+";
            } else {
                echo ".";
            }
        }
    }
}
