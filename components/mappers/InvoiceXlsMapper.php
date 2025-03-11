<?php

namespace app\components\mappers;

class InvoiceXlsMapper
{
    protected static array $mappings = [
        "0"  => "company",
        "1"  => "region",
        "2"  => "city",
        "3"  => "invoice_date",
        "4"  => "delivery_address",
        "5"  => "client_legal_address",
        "6"  => "client_name",
        "7"  => "client_code",
        "8"  => "client_subdivision_code",
        "9"  => "client_okpo",
        "10" => "product_license",
        "11" => "product_license_exp_date",
        "12" => "product_code",
        "13" => "product_barcode",
        "14" => "product_name",
        "15" => "product_morion_code",
        "16" => "product_unit",
        "17" => "product_manufacturer",
        "18" => "product_supplier",
        "19" => "product_quantity",
        "20" => "warehouse_branch",
    ];

    public static function map(array $row): array
    {
        $hash = [];

        foreach ($row as $key => $value) {
            $hash[self::$mappings[$key]] = $value  ?? null;
        }

        $hash['row_checksum'] = md5(preg_replace('/\s+/', '', implode("", $hash)));

        return $hash ?? [];
    }
}
