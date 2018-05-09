<?php
/**
 * @author Timur Kasumov (aka XAKEPEHOK)
 * Datetime: 09.05.2018 22:10
 */

namespace DjinORM\Components\Pagination\Conditions;


class BetweenCondition implements ConditionInterface
{

    /**
     * @var string
     */
    protected $field;
    protected $firstValue;
    protected $lastValue;

    public function __construct(string $field, $firstValue, $lastValue)
    {
        $this->field = $field;
        $this->firstValue = $firstValue;
        $this->lastValue = $lastValue;
    }

    /**
     * @return string
     */
    public function getField(): string
    {
        return $this->field;
    }

    /**
     * @return mixed
     */
    public function getFirstValue()
    {
        return $this->firstValue;
    }

    /**
     * @return mixed
     */
    public function getLastValue()
    {
        return $this->lastValue;
    }

}