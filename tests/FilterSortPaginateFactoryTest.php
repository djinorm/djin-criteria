<?php
/**
 * @author Timur Kasumov (aka XAKEPEHOK)
 * Datetime: 12.05.2018 21:10
 */

namespace DjinORM\Components\FilterSortPaginate;

use DjinORM\Components\FilterSortPaginate\Exceptions\UnsupportedFilterException;
use DjinORM\Components\FilterSortPaginate\Filters\AndFilter;
use DjinORM\Components\FilterSortPaginate\Filters\BetweenFilter;
use DjinORM\Components\FilterSortPaginate\Filters\CompareFilter;
use DjinORM\Components\FilterSortPaginate\Filters\EmptyFilter;
use DjinORM\Components\FilterSortPaginate\Filters\EqualsFilter;
use DjinORM\Components\FilterSortPaginate\Filters\FilterInterface;
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

    /** @var Paginate */
    private $paginate;

    /** @var Sort */
    private $sort;

    /** @var FilterInterface */
    private $filter;
    
    /** @var FilterSortPaginate */
    private $fsp;

    /** @var FilterSortPaginateFactory */
    private $factory;

    protected function setUp(): void 
    {
        parent::setUp();
        
        $this->array = [
            'paginate' => [
                'number' => 10,
                'size' => 50,
            ],
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

        $this->paginate = new Paginate(10, 50);

        $this->sort = new Sort(['field_1' => Sort::SORT_DESC, 'field_2' => Sort::SORT_ASC]);
        
        $this->filter = new OrFilter([
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

        $this->fsp = new FilterSortPaginate($this->paginate, $this->sort, $this->filter);

        $this->factory = new FilterSortPaginateFactory();
    }

    public function testGetFilters()
    {
        $this->assertCount(11, $this->factory->getFilters());
    }

    public function testAddFilter()
    {
        $this->factory->addFilter('$test', function (string $field, array $params) {
            return new class($field, $params[0]) implements FilterInterface {
                public function __construct(string $field, $param)
                {

                }
            };
        });
        $this->assertCount(12, $this->factory->getFilters());
    }

    public function testRemoveFilter()
    {
        $this->factory->removeFilter('$between');
        $this->assertCount(10, $this->factory->getFilters());
    }

    public function testCreate()
    {
        $this->assertEquals(
            $this->fsp,
            $this->factory->create($this->array)
        );
    }

    public function testCreateFewConditionsWithoutAnd()
    {
        $array = [
            'paginate' => [
                'number' => 10,
                'size' => 50
            ],
            'filters' => [
                'field_1' => ['$equals' => 1],
                'field_2' => ['$equals' => 2],
            ],
        ];

        $expected = new FilterSortPaginate(new Paginate(10, 50), null, new AndFilter([
            new EqualsFilter('field_1', 1),
            new EqualsFilter('field_2', 2),
        ]));

        $this->assertEquals($expected, $this->factory->create($array));
    }

    public function testCreateWhitelist()
    {
        $paginate = $paginate = new Paginate(10, 50);
        $sort = new Sort(['field_1' => Sort::SORT_DESC]);
        $filter = new OrFilter([
            new BetweenFilter('field_1', '2018-01-01', '2018-12-31'),
            new AndFilter([
                new NotEmptyFilter('field_1'),
                new CompareFilter('field_1', CompareFilter::LESS_THAN, 10000),
            ]),
        ]);
        $fspExpected = new FilterSortPaginate($paginate, $sort, $filter);

        $fspActual = $this->factory->create($this->array, FilterSortPaginateFactory::LIST_WHITE, ['field_1']);

        $this->assertEquals($fspExpected, $fspActual);
    }

    public function testCreateBlacklist()
    {
        $paginate = $paginate = new Paginate(10, 50);
        $sort = new Sort(['field_2' => Sort::SORT_ASC]);
        $filter = new OrFilter([
            new AndFilter([
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
            new BetweenFilter('datetime', '2018-01-01', '2018-12-31')
        ]);
        $fspExpected = new FilterSortPaginate($paginate, $sort, $filter);

        $fspActual = $this->factory->create($this->array, FilterSortPaginateFactory::LIST_BLACK, ['field_1']);

        $this->assertEquals($fspExpected, $fspActual);
    }

    public function testCreateEmptyConfig()
    {
        $this->assertEquals(
            new FilterSortPaginate(new Paginate(1, 20)),
           $this->factory->create([])
        );

        $factory = new FilterSortPaginateFactory(50);
        $this->assertEquals(
            new FilterSortPaginate(new Paginate(1, 50)),
            $factory->create([])
        );
    }

    public function testCreateWithoutPagination()
    {
        $this->assertEquals(
            new FilterSortPaginate(),
            $this->factory->create(['paginate' => null])
        );
    }

    public function testCreateUnsupportedFilter()
    {
        $this->expectException(UnsupportedFilterException::class);
        $this->factory->create([
            'filters' => [
                'datetime' => ['$unsupportedFilter' => ['2018-01-01', '2018-12-31']],
            ]
        ]);
    }

}
