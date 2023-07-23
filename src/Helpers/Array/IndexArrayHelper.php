<?php

declare(strict_types=1);

namespace Panelop\Core\Helpers\Array;

use function array_reduce;
use function ksort;

final class IndexArrayHelper
{
    public function index(
        array $array,
        ?string $keyName = null,
        ?callable $getKeyCallable = null,
        bool $kSort = false
    ): array {
        if (null === $keyName && null === $getKeyCallable) {
            return $array;
        }

        if (null === $getKeyCallable) {
            $getKeyCallable = static fn (array $item): mixed => $item[$keyName];
        }

        $result = array_reduce($array, static function (array $carry, mixed $item) use ($getKeyCallable) {
            $key = $getKeyCallable($item);
            $carry[$key] = $item;

            return $carry;
        }, []);

        if ($kSort) {
            ksort($result);
        }

        return $result;
    }
}
