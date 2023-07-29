<?php

declare(strict_types=1);

namespace Panelop\Core\Tests\Helpers\InvocationMethod;

use Panelop\Core\Helpers\InvocationMethod\ParameterSortHelper;
use Panelop\Core\Interceptor\InvocationParameter;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

use function array_map;
use function shuffle;

#[CoversClass(ParameterSortHelper::class)]
final class ParameterSortHelperTest extends TestCase
{
    public function testByPosition(): void
    {
        $parameterData = [
            0 => [0, 'first', false, true, 1],
            1 => [1, 'seconds', false, true, 2],
            2 => [2, 'third', false, true, 3],
            3 => [3, 'fourth', false, true, 4],
            4 => [4, 'fifth', true, false, null],
        ];

        $parameters = array_map(
            static fn(array $item): InvocationParameter => new InvocationParameter(...$item),
            $parameterData
        );

        shuffle($parameters);

        $sorted = (new ParameterSortHelper())->byPosition(...$parameters);

        self::assertSameSize($parameters, $sorted);

        $index = 0;
        foreach ($sorted as $parameter) {
            /** @var InvocationParameter $parameter */
            self::assertSame($parameterData[$index][0], $parameter->getPosition());
            self::assertSame($parameterData[$index][1], $parameter->getName());
            self::assertSame($parameterData[$index][2], $parameter->isVariadic());
            self::assertSame($parameterData[$index][3], $parameter->isActive());
            self::assertSame($parameterData[$index][4], $parameter->getValue());

            ++$index;
        }
    }
}
