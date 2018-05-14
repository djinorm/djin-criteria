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
     * @var Paginate|null
     */
    protected $paginate;
    /**
     * @var Sort|null
     */
    protected $sort;
    /**
     * @var FilterInterface|null
     */
    protected $filter;

    public function __construct(Paginate $paginate = null, Sort $sort = null, FilterInterface $filter = null)
    {
        $this->paginate = $paginate;
        $this->sort = $sort;
        $this->filter = $filter;
    }

    /**
     * @return Paginate|null
     */
    public function getPaginate(): ?Paginate
    {
        return $this->paginate;
    }

    /**
     * @param Paginate|null $paginate
     * @return FilterSortPaginate
     */
    public function setPaginate(?Paginate $paginate): FilterSortPaginate
    {
        $this->paginate = $paginate;
        return $this;
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

}