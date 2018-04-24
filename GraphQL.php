<?php

namespace dastanaron\GraphQL;

class GraphQL
{

    protected $filter = array();
    protected $select = array();
    protected $base = '';
    protected $query = '';



    public function __construct($base, $filter, $select)
    {
        $this->base = $base;
        $this->filter = $filter;
        $this->select = $select;
        $this->buildQuery();
    }
    
    public function __toString()
    {
        return $this->query;
    }

    public function buildQuery()
    {
        $this->query =  '{'.$this->base . $this->filter() . $this->select() . '}';
        return $this;
    }

    public function getQuery()
    {
        return $this->query;
    }

    public function filter()
    {

        $string = '(';

        $totalIteration = 1;

        $totalCount = count($this->filter);

        foreach($this->filter as $key => $element) {

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
