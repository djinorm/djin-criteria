<?php
/**
 * @author Timur Kasumov (aka XAKEPEHOK)
 * Datetime: 09.05.2018 22:18
 */

namespace DjinORM\Components\Criteria\Conditions;


use DjinORM\Components\FilterSortPaginate\Filters\FilterInterface;
use DjinORM\Components\FilterSortPaginate\Exceptions\InvalidComparatorException;

class CompareFilter implements FilterInterface
{

    const EQUALS = '=';
    const GREAT_THAN = '>';
    const GREAT_OR_EQUALS_THAN = '>=';
    const LESS_THAN = '<=';
    const LESS_OR_EQUALS_THAN = '<=';

    /**
     * @var string
     */
    protected $field;
    /**
     * @var string
     */
    protected $comparator;
    protected $value;

    public function __construct(string $field, string $comparator, $value)
    {
        $this->field = $field;
        $this->comparator = $comparator;
        $this->value = $value;
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
    public function getComparator(): string
    {
        return $this->comparator;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param string $comparator
     * @throws InvalidComparatorException
     */
    protected function guardInvalidComparator(string $comparator)
    {
        $comparators = [
            self::EQUALS,
            self::GREAT_THAN,
            self::GREAT_OR_EQUALS_THAN,
            self::LESS_THAN,
            self::LESS_OR_EQUALS_THAN,
        ];

        if (!in_array($comparator, $comparators, true)) {
            throw new InvalidComparatorException("Comparator «{$comparator}» is invalid");
        }
    }


}