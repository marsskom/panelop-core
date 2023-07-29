<?php

declare(strict_types=1);

namespace Panelop\Core\Tests\Unit\DataProviders\Interceptor;

final class InterceptorAroundDataProvider
{
    public static function dataProvider(): iterable
    {
        yield "First Bunch Of Commands" => [
            'commands' => [
                ['add', ["first"]],
                ['add', ["second"]],
            ],
            'logDataExpected' => [
                [
                    'parameters' => ["value"],
                    'arguments' => ["first"],
                ],
                [
                    'parameters' => ["value"],
                    'arguments' => ["second"],
                ],
            ],
        ];

        yield "Second Bunch Of Commands" => [
            'commands' => [
                ['get', []],
            ],
            'logDataExpected' => [
                ['result' => false],
            ],
        ];

        yield "Third Bunch Of Commands" => [
            'commands' => [
                ['clear', []],
            ],
            'logDataExpected' => [
                ['clear' => true],
            ],
        ];

        yield "Fourth Bunch Of Commands" => [
            'commands' => [
                ['get', []],
                ['add', ["first"]],
                ['add', ["second"]],
                ['get', []],
                ['add', [1122]],
                ['add', ["Hello, world!"]],
                ['clear', []],
                ['get', []],
                ['add', ["last"]],
                ['get', []],
            ],
            'logDataExpected' => [
                ['result' => false],
                [
                    'parameters' => ["value"],
                    'arguments' => ["first"],
                ],
                [
                    'parameters' => ["value"],
                    'arguments' => ["second"],
                ],
                ['result' => true],
                [
                    'parameters' => ["value"],
                    'arguments' => [1122],
                ],
                [
                    'parameters' => ["value"],
                    'arguments' => ["Hello, world!"],
                ],
                ['clear' => true],
                ['result' => false],
                [
                    'parameters' => ["value"],
                    'arguments' => ["last"],
                ],
                ['result' => true],
            ],
        ];
    }
}
