<?php
/**
 * @author Timur Kasumov (aka XAKEPEHOK)
 * Datetime: 09.05.2018 21:52
 */

namespace DjinORM\Components\FilterSortPaginate\Filters;


class EmptyFilter implements FilterInterface
{

    /**
     * @var string
     */
    protected $field;

    /**
     * EmptyFilter constructor.
     * @param string $field
     */
    public function __construct(string $field)
    {
        $this->field = $field;
    }

    /**
     * @return string
     */
    public function getField(): string
    {
        return $this->field;
    }

}