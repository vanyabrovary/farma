<?php

namespace app\models\elastic;

trait IndexTrait
{
    public static function updateMapping()
    {
        $db = static::getDb();
        $command = $db->createCommand();

        $command->setMapping(static::index(), static::type(), static::mapping());
    }

    public static function createIndex()
    {
        $db = static::getDb();
        $command = $db->createCommand();

        $command->createIndex(static::index(), ['mappings' => static::mapping()]);
    }

    public static function deleteIndex()
    {
        $db = static::getDb();
        $command = $db->createCommand();

        $command->deleteIndex(static::index(), static::type());
    }

}
