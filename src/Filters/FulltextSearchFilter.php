<?php
/**
 * @author Timur Kasumov (aka XAKEPEHOK)
 * Datetime: 09.05.2018 22:13
 */

namespace DjinORM\Components\FilterSortPaginate\Filters;


class FulltextSearchFilter implements FilterInterface
{

    /**
     * @var string
     */
    protected $field;
    /**
     * @var string
     */
    protected $search;

    /**
     * FulltextSearchFilter constructor.
     * @param string $field
     * @param string $search
     */
    public function __construct(string $field, string $search)
    {
        $this->field = $field;
        $this->search = $search;
    }

    /**
     * @return string
     */
    public function getField(): string
    {
        return $this->field;
    }

    /**
     * @return string
     */
    public function getSearch(): string
    {
        return $this->search;
    }

}