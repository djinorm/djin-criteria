<?php
/**
 * @author Timur Kasumov (aka XAKEPEHOK)
 * Datetime: 09.05.2018 21:21
 */

namespace DjinORM\Repositories\Sql;


use DjinORM\Components\FilterSortPaginate\Filters\FilterInterface;
use DjinORM\Components\FilterSortPaginate\Sort;

class FSP
{

    /**
     * @var int
     */
    protected $pageNumber;
    /**
     * @var int
     */
    protected $pageSize;
    /**
     * @var Sort
     */
    protected $sort;
    /**
     * @var FilterInterface[]
     */
    protected $filters;

    public function __construct(int $pageNumber = 1, int $pageSize = 20, Sort $sort = null, array $filters = [])
    {
        $this->pageNumber = $pageNumber;
        $this->pageSize = $pageSize;
        $this->sort = $sort ?? new Sort();
        $this->filters = $filters;
    }

    /**
     * @return Sort
     */
    public function getSort(): Sort
    {
        return $this->sort;
    }

    /**
     * @param Sort $sort
     * @return FSP
     */
    public function setSort(Sort $sort): FSP
    {
        $this->sort = $sort;
        return $this;
    }

    /**
     * @return FilterInterface[]
     */
    public function getFilters(): array
    {
        return $this->filters;
    }

    /**
     * @param FilterInterface $condition
     * @return FSP
     */
    public function addFilter(FilterInterface $condition): self
    {
        $this->filters[] = $condition;
        return $this;
    }

    /**
     * @return FSP
     */
    public function clearFilters(): self
    {
        $this->filters = [];
        return $this;
    }

    /**
     * @return int
     */
    public function getPageNumber(): int
    {
        return $this->pageNumber;
    }

    /**
     * @param int $pageNumber
     * @return FSP
     */
    public function setPageNumber(int $pageNumber): FSP
    {
        $this->pageNumber = $pageNumber;
        return $this;
    }

    /**
     * @return int
     */
    public function getPageSize(): int
    {
        return $this->pageSize;
    }

    /**
     * @param int $pageSize
     * @return FSP
     */
    public function setPageSize(int $pageSize): FSP
    {
        $this->pageSize = $pageSize;
        return $this;
    }

}