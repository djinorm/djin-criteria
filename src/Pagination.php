<?php
/**
 * @author Timur Kasumov (aka XAKEPEHOK)
 * Datetime: 09.05.2018 21:21
 */

namespace DjinORM\Repositories\Sql;


use DjinORM\Components\Pagination\Conditions\ConditionInterface;
use DjinORM\Components\Pagination\Sort;

class Pagination
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
     * @var ConditionInterface[]
     */
    protected $conditions;

    public function __construct(int $pageNumber = 1, int $pageSize = 20, Sort $sort = null, array $conditions = [])
    {
        $this->pageNumber = $pageNumber;
        $this->pageSize = $pageSize;
        $this->sort = $sort ?? new Sort();
        $this->conditions = $conditions;
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
     * @return Pagination
     */
    public function setSort(Sort $sort): Pagination
    {
        $this->sort = $sort;
        return $this;
    }

    /**
     * @return ConditionInterface[]
     */
    public function getConditions(): array
    {
        return $this->conditions;
    }

    /**
     * @param ConditionInterface $condition
     * @return Pagination
     */
    public function addCondition(ConditionInterface $condition): self
    {
        $this->conditions[] = $condition;
        return $this;
    }

    /**
     * @return Pagination
     */
    public function clearConditions(): self
    {
        $this->conditions = [];
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
     * @return Pagination
     */
    public function setPageNumber(int $pageNumber): Pagination
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
     * @return Pagination
     */
    public function setPageSize(int $pageSize): Pagination
    {
        $this->pageSize = $pageSize;
        return $this;
    }

}