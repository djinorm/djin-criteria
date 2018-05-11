<?php
/**
 * @author Timur Kasumov (aka XAKEPEHOK)
 * Datetime: 09.05.2018 22:32
 */

namespace DjinORM\Components\FilterSortPaginate\Filters;


class InFilter implements FilterInterface
{

    /**
     * @var string
     */
    private $field;
    /**
     * @var array
     */
    private $values;

    public function __construct(string $field, array $values)
    {
        $this->field = $field;
        $this->values = $values;
    }

    /**
     * @return string
     */
    public function getField(): string
    {
        return $this->field;
    }

    /**
     * @return array
     */
    public function getValues(): array
    {
        return $this->values;
    }

}