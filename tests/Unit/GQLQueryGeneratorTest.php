<?php
declare(strict_types=1);

namespace Tests\Unit;

require_once (__DIR__. '/../../vendor/autoload.php');

use dastanaron\GraphQL\GQLQueryGenerator;
use PHPUnit\Framework\TestCase;

class GQLQueryGeneratorTest extends TestCase
{
    public function testQueryGenerator(): void
    {
        $queryGenerator = new GQLQueryGenerator('user', [
            'lang'  => 'ru',
            'foo'   => 'bar',
            'limit' => 10,
        ], [
            'name',
            'age',
            'documents' =>
                [
                    'passport' => [
                        'number',
                        'serial',
                    ],
                    'driver_license' => [
                        'number',
                    ],
                    'other',
                    'photo(preset: "55x55")',
                ],
        ]);
        $formedQuery = $queryGenerator->getQuery();
        $this->assertSame(
            '{user(lang: "ru", foo: "bar", limit: 10){name age documents {passport {number serial } driver_license {number } other photo(preset: "55x55") }}}',
            $formedQuery
        );
    }

    public function testRealRequest(): void
    {
        $queryGenerator = new GQLQueryGenerator('realty_living_objects', [
            'page' => 1,
            'limit' => 15,
            'residential_complex_id' => ['=', '1'],
        ], [
            'current_page',
            'per_page',
            'total',
            'data' => [
                'id',
                'district' => [
                    'id',
                    'name',
                ],
                'object_category' => [
                    'id',
                    'name',
                ],
                'floor',
                'area'
            ],
        ]);
        $formedQuery = $queryGenerator->getQuery();
        $this->assertSame(
            '{realty_living_objects(page: 1, limit: 15, residential_complex_id: ["=", "1"]){current_page per_page total data {id district {id name } object_category {id name } floor area }}}',
            $formedQuery);
    }
}
