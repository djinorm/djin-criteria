```
{
  "$or": [
    {"$or": [
      {"updatedAt": {"$equal": "2017-01-05"}},
      {"updatedAt": {"$between": ["2017-02-01","2017-02-09"]}}
    ]},
    {"$and": [
      {"initPrice": {"$equal": "1000"}},
      {"price": {"$equal": "1500"}}
    ]}
  ]
}
```