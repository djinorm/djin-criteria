<?php
/**
 * @author Timur Kasumov (aka XAKEPEHOK)
 * Datetime: 12.05.2018 19:27
 */

namespace DjinORM\Components\FilterSortPaginate;


use Adbar\Dot;
use DjinORM\Components\FilterSortPaginate\Exceptions\InvalidListTypeException;
use DjinORM\Components\FilterSortPaginate\Exceptions\ParseException;
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

    const LIST_BLACK = 0;
    const LIST_WHITE = 1;

    /** @var Dot */
    protected $data;

    /** @var FilterSortPaginate */
    protected $fsp;

    /** @var int */
    protected $listType;

    /** @var array */
    protected $fields;

    /**
     * FilterSortPaginateFactory constructor.
     * @param array $data
     * @param int $listType
     * @param array $fields
     * @throws InvalidListTypeException
     */
    public function __construct(array $data, $listType = self::LIST_BLACK, $fields = [])
    {
        $this->data = new Dot($data);

        if (!in_array($listType, [self::LIST_BLACK, self::LIST_WHITE])) {
            throw new InvalidListTypeException("Invalid list type «{$listType}»");
        }

        $this->listType = $listType;
        $this->fields = $fields;
    }

    /**
     * @return FilterSortPaginate
     * @throws ParseException
     * @throws UnsupportedFilterException
     */
    public function create(): FilterSortPaginate
    {
        if ($this->data->has('paginate')) {
            if ($this->data->get('paginate')) {
                $paginate = new Paginate(
                    $this->data->get('paginate.number', 1),
                    $this->data->get('paginate.size', 20)
                );
            } else {
                $paginate = null;
            }
        } else {
            $paginate = new Paginate(1, 20);
        }

        if ($this->data->has('sort')) {
            $sort = new Sort();
            foreach ($this->data->get('sort') as $sortBy => $sortDirection) {
                if ($this->canUseField($sortBy)) {
                    $sort->add($sortBy, (int) $sortDirection);
                }
            }
            if (empty($sort->get())) {
                $sort = null;
            }
        } else {
            $sort = null;
        }

        if ($this->data->has('filters')) {
            $filters = $this->parse($this->data->get('filters'));
        } else {
            $filters = null;
        }

        return new FilterSortPaginate($paginate, $sort, $filters);
    }

    /**
     * @param array $filtersArray
     * @return FilterInterface
     * @throws UnsupportedFilterException
     * @throws ParseException
     */
    protected function parse(array $filtersArray): ?FilterInterface
    {
        foreach ($filtersArray as $fieldOrOperation => $conditions) {
            switch ($fieldOrOperation) {
                case '$or':
                    return $this->operationFilter($conditions, OrFilter::class);
                case '$and':
                    return $this->operationFilter($conditions, AndFilter::class);
                default:
                    if (!$this->canUseField($fieldOrOperation)) {
                        return null;
                    }
                    return $this->parseField($fieldOrOperation, $conditions);
            }
        }

        throw new ParseException('Fail to parse Filter-Sort-Paginate query');
    }

    /**
     * @param array $conditions
     * @param $filterClass
     * @return FilterInterface|null
     * @throws ParseException
     * @throws UnsupportedFilterException
     */
    protected function operationFilter(array $conditions, $filterClass): ?FilterInterface
    {
        $filters = [];
        $this->guardNotArray($conditions);
        foreach ($conditions as $subFieldOrOperation => $condition) {
            $filter = $this->parse([$subFieldOrOperation => $condition]);
            if ($filter) {
                $filters[] = $filter;
            }
        }

        if (empty($filters)) {
            return null;
        }

        if (count($filters) == 1) {
            return reset($filters);
        }

        return new $filterClass($filters);
    }

    /**
     * @param string $field
     * @param array $conditions
     * @return FilterInterface
     * @throws UnsupportedFilterException
     */
    protected function parseField(string $field, array $conditions): FilterInterface
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
    protected function createFilter(string $field, string $filter, $params): FilterInterface
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
                return $params[0] ? new EmptyFilter($field) : new NotEmptyFilter($field);
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
            case '$notEquals':
                return new NotEqualsFilter($field, $params[0]);
            case '$notIn':
                return new NotInFilter($field, $params);
            case '$notWildcard':
                return new NotWildcardFilter($field, $params[0]);
            default:
                return $this->customFilters($field, $filter, $params);
        }
    }

    /**
     * @param string $field
     * @param string $filter
     * @param array $params
     * @return FilterInterface
     * @throws UnsupportedFilterException
     */
    protected function customFilters(string $field, string $filter, array $params): FilterInterface
    {
        throw new UnsupportedFilterException("Filter «{$filter}» was not supported in this implemention");
    }

    protected function canUseField(string $field): bool
    {
        $whiteApprove = $this->listType == self::LIST_WHITE && in_array($field, $this->fields);
        $blackApprove = $this->listType == self::LIST_BLACK && !in_array($field, $this->fields);
        return $whiteApprove || $blackApprove;
    }

    /**
     * @param $variable
     * @throws ParseException
     */
    protected function guardNotArray($variable)
    {
        if (!is_array($variable)) {
            throw new ParseException('Fail to parse field query string');
        }
    }

}