<?php
/**
 * @author Timur Kasumov (aka XAKEPEHOK)
 * Datetime: 09.05.2018 22:05
 */

namespace DjinORM\Components\Pagination\Conditions;


class EqualsCondition implements ConditionInterface
{

    /**
     * @var string
     */
    protected $field;
    protected $value;

    public function __construct(string $field, $value)
    {
        $this->field = $field;
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
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

}