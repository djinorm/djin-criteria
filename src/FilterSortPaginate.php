<?php
/**
 * @author Timur Kasumov (aka XAKEPEHOK)
 * Datetime: 09.05.2018 21:21
 */

namespace DjinORM\Components\FilterSortPaginate;


use DjinORM\Components\FilterSortPaginate\Filters\FilterInterface;

class FilterSortPaginate
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
     * @var Sort|null
     */
    protected $sort;
    /**
     * @var FilterInterface|null
     */
    protected $filter;

    public function __construct(int $pageNumber = 1, int $pageSize = 20, Sort $sort = null, FilterInterface $filter = null)
    {
        $this->pageNumber = $pageNumber;
        $this->pageSize = $pageSize;
        $this->sort = $sort;
        $this->filter = $filter;
    }

    /**
     * @return Sort|null
     */
    public function getSort(): ?Sort
    {
        return $this->sort;
    }

    /**
     * @param Sort $sort
     * @return FilterSortPaginate
     */
    public function setSort(Sort $sort = null): FilterSortPaginate
    {
        $this->sort = $sort;
        return $this;
    }

    /**
     * @return FilterInterface|null
     */
    public function getFilter(): ?FilterInterface
    {
        return $this->filter;
    }

    /**
     * @param FilterInterface $filter
     * @return FilterSortPaginate
     */
    public function setFilter(FilterInterface $filter = null): self
    {
        $this->filter = $filter;
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
     * @return FilterSortPaginate
     */
    public function setPageNumber(int $pageNumber): FilterSortPaginate
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
     * @return FilterSortPaginate
     */
    public function setPageSize(int $pageSize): FilterSortPaginate
    {
        $this->pageSize = $pageSize;
        return $this;
    }

}