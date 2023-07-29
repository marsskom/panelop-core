<?php

declare(strict_types=1);

namespace Panelop\Core\Tests\Helpers\Array;

use Panelop\Core\Helpers\Array\IndexArrayHelper;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

use function random_int;
use function shuffle;
use function str_starts_with;

#[CoversClass(IndexArrayHelper::class)]
final class IndexArrayHelperTest extends TestCase
{
    public static function dataProvider(): array
    {
        for ($i = 1; $i <= 20; ++$i) {
            $data['array'][] = [
                'id' => $i,
                'name' => "Test #$i",
                'age' => random_int(8, 56),
            ];
        }

        return ['Data' => $data];
    }

    #[DataProvider('dataProvider')]
    public function testWithoutParams(
        array $array
    ): void {
        self::assertSame(
            $array,
            (new IndexArrayHelper())->index($array)
        );
    }

    #[DataProvider('dataProvider')]
    public function testByKeyName(
        array $array
    ): void {
        $result = (new IndexArrayHelper())->index($array, 'name');

        foreach ($result as $name => $data) {
            self::assertTrue(str_starts_with($name, "Test #"));
            self::assertEquals("Test #{$data['id']}", $name);
            self::assertSame($data['name'], $name);

            self::assertIsInt($data['id']);
            self::assertIsInt($data['age']);
            self::assertTrue($data['age'] >= 8);
            self::assertTrue($data['age'] <= 56);
        }
    }

    #[DataProvider('dataProvider')]
    public function testByKeyIdAndKSort(
        array $array
    ): void {
        shuffle($array);
        $result = (new IndexArrayHelper())->index($array, 'id', kSort: true);

        $lastId = -1;
        foreach ($result as $id => $data) {
            self::assertIsInt($id);
            self::assertSame($data['id'], $id);
            self::assertTrue($id > $lastId);

            $lastId = $id;
        }
    }

    #[DataProvider('dataProvider')]
    public function testByCallableAndKSort(
        array $array
    ): void {
        shuffle($array);
        $result = (new IndexArrayHelper())->index(
            $array,
            getKeyCallable: static fn(array $item): int => $item['id'],
            kSort: true
        );

        $lastId = -1;
        foreach ($result as $id => $data) {
            self::assertIsInt($id);
            self::assertSame($data['id'], $id);
            self::assertTrue($id > $lastId);

            $lastId = $id;
        }
    }

    #[DataProvider('dataProvider')]
    public function testWithKeyNameAndCallable(
        array $array
    ): void {
        shuffle($array);
        $result = (new IndexArrayHelper())->index(
            $array,
            'name',
            static fn(array $item): int => $item['id'],
            true
        );

        $lastId = -1;
        foreach ($result as $id => $data) {
            self::assertIsInt($id);
            self::assertSame($data['id'], $id);
            self::assertTrue($id > $lastId);

            $lastId = $id;
        }
    }
}
