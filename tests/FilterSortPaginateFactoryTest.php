<?php
/**
 * @author Timur Kasumov (aka XAKEPEHOK)
 * Datetime: 12.05.2018 21:10
 */

namespace DjinORM\Components\FilterSortPaginate;

use DjinORM\Components\FilterSortPaginate\Exceptions\ParseException;
use DjinORM\Components\FilterSortPaginate\Exceptions\UnsupportedFilterException;
use DjinORM\Components\FilterSortPaginate\Filters\AndFilter;
use DjinORM\Components\FilterSortPaginate\Filters\BetweenFilter;
use DjinORM\Components\FilterSortPaginate\Filters\CompareFilter;
use DjinORM\Components\FilterSortPaginate\Filters\EmptyFilter;
use DjinORM\Components\FilterSortPaginate\Filters\EqualsFilter;
use DjinORM\Components\FilterSortPaginate\Filters\FulltextSearchFilter;
use DjinORM\Components\FilterSortPaginate\Filters\InFilter;
use DjinORM\Components\FilterSortPaginate\Filters\NotBetweenFilter;
use DjinORM\Components\FilterSortPaginate\Filters\NotEmptyFilter;
use DjinORM\Components\FilterSortPaginate\Filters\NotEqualsFilter;
use DjinORM\Components\FilterSortPaginate\Filters\NotInFilter;
use DjinORM\Components\FilterSortPaginate\Filters\NotWildcardFilter;
use DjinORM\Components\FilterSortPaginate\Filters\OrFilter;
use DjinORM\Components\FilterSortPaginate\Filters\WildcardFilter;
use PHPUnit\Framework\TestCase;

class FilterSortPaginateFactoryTest extends TestCase
{
    
    /** @var array */
    private $array;
    
    /** @var FilterSortPaginate */
    private $fsp;

    protected function setUp(): void 
    {
        parent::setUp();
        
        $this->array = [
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
                            'field_8' => ['$wildcard' => '*hello _____!'],
                            'field_9' => ['$notBetween' => [100, 200]],
                            'field_10' => ['$notEquals' => 'not-value'],
                            'field_11' => ['$notIn' => [9, 8, 7]],
                            'field_12' => ['$notWildcard' => '*hello _____!'],
                        ],
                        'field_1' => [
                            '$empty' => false,
                            '$compare' => [CompareFilter::LESS_THAN, 10000],
                        ],
                    ],
                    'datetime' => ['$between' => ['2018-01-01', '2018-12-31']],
                ],
            ],
        ];

        $sort = new Sort(['field_1' => Sort::SORT_DESC, 'field_2' => Sort::SORT_ASC]);
        
        $filter = new OrFilter([
            new OrFilter([
                new AndFilter([
                    new BetweenFilter('field_1', '2018-01-01', '2018-12-31'),
                    new CompareFilter('field_2', CompareFilter::GREAT_THAN, 500),
                    new EmptyFilter('field_3'),
                    new NotEmptyFilter('field_4'),
                    new EqualsFilter('field_5', 'value'),
                    new FulltextSearchFilter('field_6', 'hello world'),
                    new InFilter('field_7', [1, 2, 3, 4, 'five', 'six']),
                    new WildcardFilter('field_8', '*hello _____!'),
                    new NotBetweenFilter('field_9', 100, 200),
                    new NotEqualsFilter('field_10', 'not-value'),
                    new NotInFilter('field_11', [9, 8, 7]),
                    new NotWildcardFilter('field_12', '*hello _____!'),
                ]),
                new AndFilter([
                    new NotEmptyFilter('field_1'),
                    new CompareFilter('field_1', CompareFilter::LESS_THAN, 10000),
                ]),
            ]),
            new BetweenFilter('datetime', '2018-01-01', '2018-12-31')
        ]);

        $this->fsp = new FilterSortPaginate(10, 50, $sort, $filter);
    }

    public function testCreate()
    {
        $factory = new FilterSortPaginateFactory($this->array);
        $this->assertEquals($this->fsp, $factory->create());
    }

    public function testCreateEmptyConfig()
    {
        $factory =  new FilterSortPaginateFactory([]);
        $this->assertEquals(
            new FilterSortPaginate(1, 20),
           $factory->create()
        );
    }

    public function testCreateUnsupportedFilter()
    {
        $this->expectException(UnsupportedFilterException::class);
        $factory =  new FilterSortPaginateFactory([
            'filters' => [
                'datetime' => ['$unsupportedFilter' => ['2018-01-01', '2018-12-31']],
            ]
        ]);
        $factory->create();
    }

    public function testCreateParseException()
    {
        $this->expectException(ParseException::class);
        $factory =  new FilterSortPaginateFactory([
            'filters' => [
                ['datetime'],
            ]
        ]);
        $factory->create();
    }

}
