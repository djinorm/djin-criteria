<?php
/**
 * Created for djin-filter-sort-paginate.
 * Datetime: 10.05.2018 14:35
 * @author Timur Kasumov aka XAKEPEHOK
 */

namespace DjinORM\Components\FilterSortPaginate\Filters;


class AndFilter implements FilterInterface
{

    /** @var FilterInterface[] */
    protected $filters;

    public function __construct(array $filters)
    {
        foreach ($filters as $filter) {
            $this->guardInvalidFilter($filter);
            if ($filter) {
                $this->filters[] = $filter;
            }
        }
    }

    /**
     * @return FilterInterface[]
     */
    public function getFilters(): array
    {
        return $this->filters;
    }

    private function guardInvalidFilter(FilterInterface $filter = null)
    {
        return $filter;
    }

}