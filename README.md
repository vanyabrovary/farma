### Описание задания
1. xls надо загрузить в монго.
2. Из монго в эластик, любые 5 колонок. 
3. Учесть возможность повторного запуска импорта/переноса - что бы не создавались дубли. 
4. Сделать агрегацию (отчет с группировкой) по области и товару, взять сумму по колонке количество. 
5. Формат реализации и вывода информации свободный - или ui или команды в консоли.

Для работы у вас есть : yii2, mongodb, elasticsearch.

### Команды запуска

#### Запуск импорта данных из xls в mongodb и elastic (пункты 1-3)
`./yii invoice/import`

#### Отображение данных в консоли (пункт 4)
`./yii search/invoice`

#### Очистка коллекции elastic
`./yii invoice/elastic-recreate-mappings`

#### Очистка коллекции MongoDB
`./yii invoice/delete-data-fom-mongo`


### Структура файлов
```
commands/
    InvoiceController.php - контроллер служит для импорта данных
    SearchController.php - контроллер служит для отображения данных

components/
    mappers/
        InvoiceXlsMapper.php - преобразование данных полученных из xls  
    parsers/
        XlsParser.php - парсер xls файлов 
models
    elastic/
        InvoiceElastic.php - модель данных в elastic
    mongo/
        InvoiceMongo.php  - модель данных в mongo
```

### TODO

- Автоматическон создание collection в mongo, если не существует.
- Ускорение загрузки данных, упрощение загрузки
- Оптимизация кода
- Добавление try/catch в необходимых местах
- Логирование вместо echo
