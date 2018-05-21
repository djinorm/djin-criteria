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

    /** @var callable[] */
    private $filters;

    /** @var int */
    protected $listType;

    /** @var array */
    protected $listFields;
    /**
     * @var int
     */
    private $defaultPageSize;

    public function __construct(int $defaultPageSize = 20)
    {
        $this->defaultPageSize = $defaultPageSize;
        $this->filters = [
            '$between' => function(string $field, array $params) {
                return new BetweenFilter($field, $params[0], $params[1]);
            },
            '$compare' => function(string $field, array $params) {
                return new CompareFilter($field, $params[0], $params[1]);
            },
            '$empty' => function(string $field, array $params) {
                return $params[0] ? new EmptyFilter($field) : new NotEmptyFilter($field);
            },
            '$equals' => function(string $field, array $params) {
                return new EqualsFilter($field, $params[0]);
            },
            '$fulltextSearch' => function(string $field, array $params) {
                return new FulltextSearchFilter($field, $params[0]);
            },
            '$in' => function(string $field, array $params) {
                return new InFilter($field, $params);
            },
            '$wildcard' => function(string $field, array $params) {
                return new WildcardFilter($field, $params[0]);
            },
            '$notBetween' => function(string $field, array $params) {
                return new NotBetweenFilter($field, $params[0], $params[1]);
            },
            '$notEquals' => function(string $field, array $params) {
                return new NotEqualsFilter($field, $params[0]);
            },
            '$notIn' => function(string $field, array $params) {
                return new NotInFilter($field, $params);
            },
            '$notWildcard' => function(string $field, array $params) {
                return new NotWildcardFilter($field, $params[0]);
            },
        ];
    }

    public function getFilters(): array
    {
        return $this->filters;
    }

    public function addFilter(string $alias, callable $callback)
    {
        $this->filters[$alias] = $callback;
    }

    public function removeFilter(string $alias)
    {
        unset($this->filters[$alias]);
    }

    /**
     * @param array $data
     * @param int $listType
     * @param array $fields
     * @return FilterSortPaginate
     * @throws InvalidListTypeException
     * @throws ParseException
     * @throws UnsupportedFilterException
     */
    public function create(array $data, $listType = self::LIST_BLACK, $fields = []): FilterSortPaginate
    {
        $data = new Dot($data);

        if (!in_array($listType, [self::LIST_BLACK, self::LIST_WHITE])) {
            throw new InvalidListTypeException("Invalid list type «{$listType}»");
        }

        $this->listType = $listType;
        $this->listFields = $fields;

        $paginate = $this->parsePaginate($data);
        $sort = $this->parseSort($data);
        $filters = $this->parseFilters($data);

        return new FilterSortPaginate($paginate, $sort, $filters);
    }

    protected function parsePaginate(Dot $data): ?Paginate
    {
        if ($data->has('paginate')) {
            if ($data->get('paginate')) {
                return new Paginate(
                    $data->get('paginate.number', 1),
                    $data->get('paginate.size', $this->defaultPageSize)
                );
            } else {
                return null;
            }
        }
        return new Paginate(1, $this->defaultPageSize);
    }

    protected function parseSort(Dot $data): ?Sort
    {
        $sort = null;
        if ($data->has('sort')) {
            $sort = new Sort();
            foreach ($data->get('sort') as $sortBy => $sortDirection) {
                if ($this->canUseField($sortBy)) {
                    $sort->add($sortBy, (int) $sortDirection);
                }
            }
            if (empty($sort->get())) {
                $sort = null;
            }
        }
        return $sort;
    }

    /**
     * @param Dot $data
     * @return FilterInterface|null
     * @throws ParseException
     * @throws UnsupportedFilterException
     */
    protected function parseFilters(Dot $data): ?FilterInterface
    {
        if ($data->has('filters')) {
            return $this->parse(['$and' => $data->get('filters', [])]);
        } else {
            return null;
        }
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

        foreach ($this->filters as $filterAlias => $callback) {
            if ($filterAlias == $filter) {
                return $callback($field, $params);
            }
        }

        throw new UnsupportedFilterException("Filter «{$filter}» was not supported in this implemention");
    }

    protected function canUseField(string $field): bool
    {
        $whiteApprove = $this->listType == self::LIST_WHITE && in_array($field, $this->listFields);
        $blackApprove = $this->listType == self::LIST_BLACK && !in_array($field, $this->listFields);
        return $whiteApprove || $blackApprove;
    }

    /**
     * @param $variable
     * @throws ParseException
     */
    private function guardNotArray($variable)
    {
        if (!is_array($variable)) {
            throw new ParseException('Fail to parse field query string');
        }
    }

    /**
     * @param array $filtersArray
     * @return FilterInterface
     * @throws UnsupportedFilterException
     * @throws ParseException
     */
    private function parse(array $filtersArray): ?FilterInterface
    {
        foreach ($filtersArray as $fieldOrOperation => $conditions) {
            $filters = [];
            if (in_array($fieldOrOperation, ['$or', '$and'])) {
                $this->guardNotArray($conditions);

                //Рекурсивный парсинг, если ключ $or или $and
                foreach ($conditions as $subFieldOrOperation => $condition) {
                    if ($filter = $this->parse([$subFieldOrOperation => $condition])) {
                        $filters[] = $filter;
                    }
                }

                if (empty($filters)) {
                    return null;
                }

                //Если в $or или $and только один фильтр, то возвращаем сразу его
                if (count($filters) == 1) {
                    return reset($filters);
                }

                //Иначе оборачиваем фильтры в $or или $and
                return $fieldOrOperation == '$and' ? new AndFilter($filters) : new OrFilter($filters);

            } else {
                //Если создаем фильтры для конкретного поля
                $field = $fieldOrOperation;

                //Проверяем, можно ли работать с этим полем в соответствии с черным или белым списком
                if (!$this->canUseField($fieldOrOperation)) {
                    return null;
                }

                //Для одного поля может быть заданно множество фильтров
                foreach ($conditions as $filter => $params) {
                    $filters[] = $this->createFilter($field, $filter, $params);
                }

                //Если задан только один фильтр, то возвращаем сразу его
                if (count($filters) == 1) {
                    return current($filters);
                }

                //Если несколько, то объединяем их через and
                return new AndFilter($filters);
            }
        }

        throw new ParseException('Fail to parse Filter-Sort-Paginate query');
    }

}