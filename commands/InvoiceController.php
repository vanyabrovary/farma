<?php

namespace app\commands;

use Yii;
use yii\console\{Controller,ExitCode};
use app\components\mappers\InvoiceXlsMapper;
use app\components\parsers\XlsParser;
use app\models\elastic\InvoiceElastic;
use app\models\mongo\InvoiceMongo;


class InvoiceController extends Controller
{

    public function actionImport()
    {
        echo "\nStart import\n";

        $data = $this->getDataFomXls();
        $this->importToMongo($data);
        $this->importToElastic();

        echo "\nData was imported\n";

        return ExitCode::OK;
    }

    public function actionElasticRecreateMappings()
    {
        echo "\nRecreate Elastic indexes\n";

        InvoiceElastic::deleteIndex();
        InvoiceElastic::createIndex();

        return ExitCode::OK;
    }

    private function getDataFomXls(): array
    {
        echo "\nGetting data from xls\n";

        $xlsFilePath = Yii::getAlias('@runtime/tmp/file.xls');
        return (new XlsParser())->parseFile($xlsFilePath, fn($row) => InvoiceXlsMapper::map($row)) ?? [];
    }

    private function importToMongo(array $data): void
    {
        echo "\nImporting data to MongoDB\n";

        foreach ($data as $rowNum => $row) {
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
