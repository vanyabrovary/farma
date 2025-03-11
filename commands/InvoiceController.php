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
        $data = $this->getDataFomXls();
        $this->importToMongo($data);
        $this->importToElastic();

        return ExitCode::OK;
    }

    public function actionElasticRecreateMappings()
    {
        InvoiceElastic::deleteIndex();
        InvoiceElastic::createIndex();

        return ExitCode::OK;
    }

    private function getDataFomXls(): array
    {
        $xlsFilePath = Yii::getAlias('@runtime/tmp/file.xls');
        return (new XlsParser())->parseFile($xlsFilePath, fn($row) => InvoiceXlsMapper::map($row)) ?? [];
    }

    private function importToMongo(array $data): void
    {
        foreach ($data as $rowNum => $row) {
            if (!InvoiceMongo::find()->where(['row_checksum' => $row['row_checksum']])->exists()) {
                $invoice = new InvoiceMongo();
                $invoice->setAttributes($row, false);
                $invoice->save();
            } else {
                echo "Row $rowNum already exists.\n";
            }
        }
    }

    private function importToElastic(): void
    {
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
                echo "Add row {$row['row_checksum']}.\n";
            } else {
                echo "Row {$row['row_checksum']} exists.\n";
            }
        }
    }
}
