<?php

namespace backend\components;

class GraphQL
{

    public $filter = array();
    public $select = array();
    public $query = '';
    public $queryString;

    public $objectFilter;

    public function __construct()
    {
        $this->objectFilter = new \stdClass();
    }

    public function buildQuery()
    {
        $this->queryString =  '{'.$this->query . $this->filter() . $this->select() . '}';
        return $this;
    }

    public function filter()
    {

        $string = '(';

        $totalIteration = 1;

        $totalCount = count($this->filter);

        foreach($this->filter as $key => $element) {

            $this->objectFilter->$key = $element;

            if(is_array($element)) {
                $string .= $key. ': [';

                $iteration = 1;
                $itemCount = count($element);

                foreach ($element as $item) {
                    $string .= '"'.$item.'"';
                    if($iteration === $itemCount) {
                        $string .= '';
                    }
                    else {
                        $string .= ', ';
                    }
                    $iteration++;
                }

                $string .= ']';

            }
            else {
                $string .= $key.': '.$this->ElementType($element);
            }

            if($totalCount !== $totalIteration) {
                $string .= ', ';
            }

            $totalIteration++;

        }

        $string .= ')';

        return $string;
    }

    public function select()
    {
        $string = '{';

        $totalIteration = 1;
        $totalCount = count($this->select);

        foreach($this->select as $key => $elem) {

            if(is_array($elem)) {
                $string .= $key.'{';

                $iteration = 1;
                $itemCount = count($elem);
                foreach ($elem as $item) {
                    $string .= $item;

                    if($iteration !== $itemCount) {
                        $string .= ' ';
                    }
                    $iteration++;
                }
                $string .= '}';
            }
            else {
                $string .= $elem;
            }

            if($totalCount !== $totalIteration) {
                $string .= ' ';
            }

            $totalIteration++;
        }

        $string .= '}';

        return $string;
    }

    protected function ElementType($var)
    {
        if(is_integer($var) || is_float($var)) {
            return $var;
        }
        else if(is_bool($var)) {
            return $var ? 'true' : 'false';
        }
        else {
            return '"'.$var.'"';
        }
    }

}
