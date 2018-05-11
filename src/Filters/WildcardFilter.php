<?php
/**
 * @author Timur Kasumov (aka XAKEPEHOK)
 * Datetime: 09.05.2018 22:09
 */

namespace DjinORM\Components\FilterSortPaginate\Filters;


class WildcardFilter implements FilterInterface
{

    const ANY = '*';
    const ONE = '?';

    /**
     * @var string
     */
    protected $field;
    /**
     * @var string
     */
    protected $wildcard;

    public function __construct(string $field, string $wildcard)
    {
        $this->field = $field;
        $this->wildcard = $wildcard;
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
    public function getWildcard(): string
    {
        return $this->wildcard;
    }

}