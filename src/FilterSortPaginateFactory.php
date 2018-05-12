<?php
/**
 * @author Timur Kasumov (aka XAKEPEHOK)
 * Datetime: 12.05.2018 19:27
 */

namespace DjinORM\Components\FilterSortPaginate;


use Adbar\Dot;
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

class FilterSortPaginateFactory
{

    /** @var Dot */
    protected $data;

    /** @var FilterSortPaginate */
    protected $fsp;

    public function __construct(array $data)
    {
        $this->data = new Dot($data);
    }

    /**
     * @return FilterSortPaginate
     * @throws UnsupportedFilterException
     */
    public function create(): FilterSortPaginate
    {
        $page = $this->data->get('page', 1);
        $pageSize = $this->data->get('pageSize', 20);

        if ($this->data->has('sort')) {
            $sort = new Sort();
            foreach ($this->data->get('sort') as $sortBy => $sortDirection) {
                $sort->add($sortBy, $sortDirection);
            }
        } else {
            $sort = null;
        }

        if ($this->data->has('filters')) {
            $jsonFilters = \json_decode($this->data->get('filters'), true);
            $filters = $this->parse($jsonFilters);
        } else {
            $filters = null;
        }

        return new FilterSortPaginate($page, $pageSize, $sort, $filters);
    }

    /**
     * @param array $filtersArray
     * @return FilterInterface
     * @throws UnsupportedFilterException
     */
    protected function parse(array $filtersArray): ?FilterInterface
    {
        foreach ($filtersArray as $fieldOrOperation => $conditions) {
            switch ($fieldOrOperation) {
                case '$or':
                    $orFilters = [];
                    foreach ($conditions as $subFieldOrOperation => $condition) {
                        $orFilters[] = $this->parse([$subFieldOrOperation => $condition]);
                    }
                    return new OrFilter($orFilters);
                case '$and':
                    $andFilters = [];
                    foreach ($conditions as $subFieldOrOperation => $condition) {
                        $andFilters[] = $this->parse([$subFieldOrOperation => $condition]);
                    }
                    return new AndFilter($andFilters);
                default:
                    return $this->parseField($fieldOrOperation, $conditions);
            }
        }

        throw new \ParseError('');
    }

    /**
     * @param string $field
     * @param array $conditions
     * @return FilterInterface
     * @throws UnsupportedFilterException
     */
    private function parseField(string $field, array $conditions): FilterInterface
    {
        $filters = [];
        foreach ($conditions as $filter => $params) {
            $filters[] = $this->createFilter($field, $filter, $params);
        }
        if (count($filters) == 1) {
            return current($filters);
        }
        return new AndFilter($filters);
    }

    /**
     * @param string $field
     * @param string $filter
     * @param $params
     * @return FilterInterface
     * @throws UnsupportedFilterException
     */
    private function createFilter(string $field, string $filter, $params): FilterInterface
    {
        if (!is_array($params)) {
            $params = [$params];
        }

        switch ($filter) {
            case '$between':
                return new BetweenFilter($field, $params[0], $params[1]);
            case '$compare':
                return new CompareFilter($field, $params[0], $params[1]);
            case '$empty':
                return new EmptyFilter($field);
            case '$equals':
                return new EqualsFilter($field, $params[0]);
            case '$fulltextSearch':
                return new FulltextSearchFilter($field, $params[0]);
            case '$in':
                return new InFilter($field, $params);
            case '$wildcard':
                return new WildcardFilter($field, $params[0]);
            case '$notBetween':
                return new NotBetweenFilter($field, $params[0], $params[1]);
            case '$notEmpty':
                return new NotEmptyFilter($field);
            case '$notEquals':
                return new NotEqualsFilter($field, $params[0]);
            case '$notIn':
                return new NotInFilter($field, $params);
            case '$notWildcard':
                return new NotWildcardFilter($field, $params[0]);
            default:
                throw new UnsupportedFilterException("Filter «{$filter}» was not supported in this implemention");
        }
    }

}