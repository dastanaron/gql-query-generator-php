<?php
declare(strict_types=1);

namespace dastanaron\GraphQL;

/**
 * Class GQLQueryGenerator
 * @package App\Library
 */
class GQLQueryGenerator
{
    /**
     * @var array
     */
    protected $filter = [];

    /**
     * @var array
     */
    protected $select = [];

    /**
     * @var string
     */
    protected $base = '';

    /**
     * @var string
     */
    protected $query = '';

    /**
     * GQLQueryGenerator constructor.
     * @param string $base
     * @param array  $filter
     * @param array  $select
     */
    public function __construct(string $base, array $filter, array $select)
    {
        $this->base = $base;
        $this->filter = $filter;
        $this->select = $select;
        $this->buildQuery();
    }

    /**
     * @return string
     */
    public function getQuery(): string
    {
        return $this->query;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->query;
    }

    private function buildQuery(): void
    {
        $this->query =  '{'.$this->base . $this->convertFilterToGQLArguments() . $this->convertSelectToGQLSelect() . '}';
    }

    /**
     * @return string
     */
    private function convertFilterToGQLArguments(): string
    {
        $convertedString = '(';

        $totalIteration = 1;

        $totalCount = count($this->filter);

        foreach($this->filter as $key => $element) {

            if(is_array($element)) {
                $convertedString .= $key. ': [';

                $iteration = 1;
                $itemCount = count($element);

                foreach ($element as $item) {
                    $convertedString .= '"'.$item.'"';
                    if($iteration === $itemCount) {
                        $convertedString .= '';
                    }
                    else {
                        $convertedString .= ', ';
                    }
                    $iteration++;
                }

                $convertedString .= ']';

            }
            else {
                $convertedString .= $key.': '.$this->getInGraphQLType($element);
            }

            if($totalCount !== $totalIteration) {
                $convertedString .= ', ';
            }

            $totalIteration++;

        }

        $convertedString .= ')';

        return $convertedString;
    }

    /**
     * @return string
     */
    private function convertSelectToGQLSelect(): string
    {
        $string = '{';

        $string .= $this->implodeRecursive(' ', $this->select);

        $string .= '}';

        return $string;
    }

    /**
     * @param $var
     * @return string|float|bool|integer
     */
    private function getInGraphQLType($var)
    {
        if(is_integer($var) || is_float($var) || is_bool($var)) {
            return $var;
        }

        return '"'.$var.'"';
    }

    /**
     * @param $glue
     * @param $parameters
     * @return string
     */
    private function implodeRecursive(string $glue, array $parameters): string
    {
        $output = '';

        foreach ($parameters as $key => $parameter) {
            if (is_array($parameter)) {

                $output .= $key . ' {'.$this->implodeRecursive($glue, $parameter) . $glue.'} ';
            } else {
                $output .= $parameter . $glue;
            }
        }

        $output = substr($output, 0, 0-strlen($glue));

        return $output;
    }
}
