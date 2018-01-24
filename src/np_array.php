<?php

namespace numphp;

use numphp\operator;
use numphp\Indexing\Indexer;
use numphp\Printing\Printer;

class np_array extends \ArrayObject
{
    use Indexer, Printer;

    public function __construct($input = [], $flags = 0, $iterator_class = "ArrayIterator")
    {
        // convert each subarray into np_array
        foreach ($input as &$item)
        {
            if (is_array($item))
                $item = new np_array($item);
        }
        return parent::__construct($input, $flags, $iterator_class);
    }

    public function __call($method, $args)
    {
        // comparations call: eq, gt. gte, lt, etc...
        // math operations call: add, sub, div, etc...
        if (in_array($method, operator::$comparations) || in_array($method, operator::$operators))
        {
            array_unshift($args, $this);
            return new self(forward_static_call_array(['numphp\operator', $method], $args));
        }
        else
            throw new \Exception("Invalid operator: " . $method);
    }
}