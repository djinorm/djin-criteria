<?php
/**
 * Created for djin-filter-sort-paginate.
 * Datetime: 10.05.2018 16:17
 * @author Timur Kasumov aka XAKEPEHOK
 */

namespace DjinORM\Components\FilterSortPaginate\Filters;

use PHPUnit\Framework\TestCase;

class AndFilterTest extends TestCase
{

    public function testConstruct()
    {
        /** @var FilterInterface $sub_1 */
        $sub_1 = $this->createMock(FilterInterface::class);

        /** @var FilterInterface $sub_2 */
        $sub_2 = $this->createMock(FilterInterface::class);
        $filter = new AndFilter($sub_1, $sub_2);

        $this->assertInstanceOf(AndFilter::class, $filter);
        $this->assertEquals([$sub_1, $sub_2], $filter->getFilters());
    }

    public function testConstructOneIncorrectClass()
    {
        /** @var FilterInterface $filter */
        $filter = $this->createMock(FilterInterface::class);

        $this->expectException(\TypeError::class);
        new AndFilter($filter, $this);
    }
}
