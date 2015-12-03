<?php

namespace Tastd\Bundle\CoreBundle\Doctrine;

use Doctrine\DBAL\Connection;

class Condition
{

    protected $parameters;
    protected $types;
    protected $conditions;
    /** @var Condition[] */
    protected $children;
    protected $and;

    /**
     * __construct
     */
    public function __construct()
    {
        $this->parameters = array();
        $this->types = array();
        $this->conditions = array();
        $this->children = array();
        $this->and = true;
    }

    /**
     * @param boolean $and
     */
    public function setAnd($and)
    {
        $this->and = $and;
    }

    public function addChild(Condition $child)
    {
        $this->children[] = $child;
    }

    /**
     * @param $condition
     * @param $value
     * @param $key
     * @param $type
     */
    public function add($condition, $value, $key, $type)
    {
        if (!isset($value)) {
            return;
        }

        if ($type === Connection::PARAM_INT_ARRAY && is_string($value)) {
            $value = explode(',', $value);
        }

        $this->conditions[$key] = $condition;
        $this->parameters[$key] = $value;
        $this->types[$key] = $type;
    }

    /**
     * @return string
     */
    public function getSql()
    {
        $logic = $this->and ? ' AND ' : ' OR ';
        $conditions = $this->conditions;
        foreach ($this->children as $child) {
            $conditions[] = ' (' . $child->getSql() . ') ';
        }

        return implode($logic, $conditions);
    }

    /**
     * @return array
     */
    public function getParameters()
    {
        $parameters = $this->parameters;
        foreach ($this->children as $child) {
            $parameters = array_merge($child->getParameters(), $parameters);
        }

        return $parameters;
    }

    /**
     * @return array
     */
    public function getTypes()
    {
        $types = $this->types;
        foreach ($this->children as $child) {
            $types = array_merge($child->getTypes(), $types);
        }

        return $types;
    }

}