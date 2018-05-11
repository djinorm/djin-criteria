<?php
/**
 * Created for djin-filter-sort-paginate.
 * Datetime: 10.05.2018 14:35
 * @author Timur Kasumov aka XAKEPEHOK
 */

namespace DjinORM\Components\FilterSortPaginate\Filters;


class OrFilter implements FilterInterface
{

    /** @var FilterInterface[] */
    protected $filters;

    public function __construct(FilterInterface ...$filters)
    {
        $this->filters = $filters;
    }

    /**
     * @return FilterInterface[]
     */
    public function getFilters(): array
    {
        return $this->filters;
    }

}