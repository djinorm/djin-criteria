<?php
/**
 * @author Timur Kasumov (aka XAKEPEHOK)
 * Datetime: 09.05.2018 21:36
 */

namespace DjinORM\Components\FilterSortPaginate;


class Sort
{

    const SORT_ASC = 1;
    const SORT_DESC = -1;

    private $sort = [];

    public function get(): array
    {
        return $this->sort;
    }

    public function add(string $sortBy, int $sortDirection = self::SORT_DESC)
    {
        $this->sort[$sortBy] = $sortDirection;
    }

    public function clear()
    {
        $this->sort = [];
    }

}