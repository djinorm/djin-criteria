<?php
/**
 * Created for djin-filter-sort-paginate.
 * Datetime: 14.05.2018 17:01
 * @author Timur Kasumov aka XAKEPEHOK
 */

namespace DjinORM\Components\FilterSortPaginate;


class Paginate
{

    protected $number;
    protected $size;

    public function __construct(int $number, int $size)
    {
        $this->number = $number;
        $this->size = $size;
    }

    /**
     * @return int
     */
    public function getNumber(): int
    {
        return $this->number;
    }

    /**
     * @param int $number
     * @return Paginate
     */
    public function setNumber(int $number): Paginate
    {
        $this->number = $number;
        return $this;
    }

    /**
     * @return int
     */
    public function getSize(): int
    {
        return $this->size;
    }

    /**
     * @param int $size
     * @return Paginate
     */
    public function setSize(int $size): Paginate
    {
        $this->size = $size;
        return $this;
    }

}