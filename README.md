PHP
```php
[
    'page' => 10,
    'pageSize' => 50,
    'sort' => [
        'field_1' => Sort::SORT_DESC,
        'field_2' => Sort::SORT_ASC,
    ],
    'filters' => [
        '$or' => [
            '$or' => [
                '$and' => [
                    'field_1' => ['$between' => ['2018-01-01', '2018-12-31']],
                    'field_2' => ['$compare' => [CompareFilter::GREAT_THAN, 500]],
                    'field_3' => ['$empty' => true],
                    'field_4' => ['$empty' => false],
                    'field_5' => ['$equals' => 'value'],
                    'field_6' => ['$fulltextSearch' => 'hello world'],
                    'field_7' => ['$in' => [1, 2, 3, 4, 'five', 'six']],
                    'field_8' => ['$wildcard' => '*hello ?????!'],
                    'field_9' => ['$notBetween' => [100, 200]],
                    'field_10' => ['$notEquals' => 'not-value'],
                    'field_11' => ['$notIn' => [9, 8, 7]],
                    'field_12' => ['$notWildcard' => '*hello ?????!'],
                ],
                'field_1' => [
                    '$empty' => false,
                    '$compare' => [CompareFilter::LESS_THAN, 10000],
                ],
            ],
            'datetime' => ['$between' => ['2018-01-01', '2018-12-31']],
        ],
    ],
]
```

JSON
```json
{
    "page": 10,
    "pageSize": 50,
    "sort": {
        "field_1": -1,
        "field_2": 1
    },
    "filters": {
        "$or": {
            "$or": {
                "$and": {
                    "field_1": {"$between": ["2018-01-01", "2018-12-31"]},
                    "field_2": {"$compare": [">", 500]},
                    "field_3": {"$empty": true},
                    "field_4": {"$empty": false},
                    "field_5": {"$equals": "value"},
                    "field_6": {"$fulltextSearch": "hello world"},
                    "field_7": {"$in": [1, 2, 3, 4, "five", "six"]},
                    "field_8": {"$wildcard": "*hello ?????!"},
                    "field_9": {"$notBetween": [100, 200]},
                    "field_10": {"$notEquals": "not-value"},
                    "field_11": {"$notIn": [9, 8, 7]},
                    "field_12": {"$notWildcard": "*hello ?????!"}
                },
                "field_1": {
                    "$empty": false,
                    "$compare": ["<", 10000]
                }
            },
            "datetime": {"$between": ["2018-01-01", "2018-12-31"]}
        }
    }
}
```