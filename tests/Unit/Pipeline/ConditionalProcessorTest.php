<?php

declare(strict_types=1);

namespace Panelop\Core\Tests\Unit\Pipeline;

use InvalidArgumentException;
use Panelop\Core\Pipeline\ConditionalProcessor;
use Panelop\Core\Pipeline\DefaultProcessor;
use Panelop\Core\Pipeline\Interfaces\PipelineInterface;
use Panelop\Core\Pipeline\Pipeline;
use Panelop\Core\Tests\App;
use Panelop\Core\Tests\Unit\Classes\Customer\CustomerAgeValidator;
use Panelop\Core\Tests\Unit\Classes\Customer\CustomerEntity;
use Panelop\Core\Tests\Unit\Classes\SimpleArrayWrapper;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(ConditionalProcessor::class)]
#[CoversClass(DefaultProcessor::class)]
#[CoversClass(Pipeline::class)]
final class ConditionalProcessorTest extends TestCase
{
    public static function customerDataProvider(): iterable
    {
        yield "Invalid data" => [
            'age' => 0,
            'isAllowToDrink' => false,
            'expectedException' => InvalidArgumentException::class,
            'expectedExceptionMessage' => "Age is invalid",
        ];

        yield "Teenager" => [
            'age' => 15,
            'isAllowToDrink' => false,
            'expectedException' => InvalidArgumentException::class,
            'expectedExceptionMessage' => "Isn't adult",
        ];

        yield "Adult" => [
            'age' => 25,
            'isAllowToDrink' => true,
            'expectedException' => '',
            'expectedExceptionMessage' => '',
        ];
    }

    /**
     * @dataProvider customerDataProvider
     */
    public function testSimpleCondition(
        int  $age,
        bool $isAllowToDrink,
    ): void {
        $processor = new ConditionalProcessor(
            // Makes pipeline with default processor.
            App::$container->get(PipelineInterface::class)
                ->pipe(static fn (CustomerEntity $customerEntity): bool => $customerEntity->getAge() > 21)
        );

        /**
         * @var CustomerEntity $customerEntity
         */
        $customerEntity = $processor(
            $age,
            static fn (int $age): CustomerEntity => (new CustomerEntity())->setAge($age),
            static fn (CustomerEntity $customerEntity): CustomerEntity => $customerEntity->setIsAllowedToDrink(),
        );

        self::assertSame($isAllowToDrink, $customerEntity->isAllowedToDrink());
    }

    /**
     * @dataProvider customerDataProvider
     */
    public function testConditionWithConditionalPipelineInside(
        int    $age,
        bool   $isAllowToDrink,
        string $expectedException,
        string $expectedExceptionMessage,
    ): void {
        if (!empty($expectedException) && !empty($expectedExceptionMessage)) {
            $this->expectException($expectedException);
            $this->expectExceptionMessage($expectedExceptionMessage);
        }

        $processor = new ConditionalProcessor(
            new Pipeline(
                new ConditionalProcessor(
                    App::$container->get(PipelineInterface::class)
                        ->pipe(
                            static fn (CustomerEntity $customerEntity): CustomerEntity => (new CustomerAgeValidator(
                            ))->validateIsAdult(
                                $customerEntity
                            )
                        )
                ),
                static fn (CustomerEntity $customerEntity): CustomerEntity => (new CustomerAgeValidator(
                ))->validateValue(
                    $customerEntity
                )
            )
        );

        /**
         * @var CustomerEntity $customerEntity
         */
        $customerEntity = $processor(
            $age,
            static fn (int $age): CustomerEntity => (new CustomerEntity())->setAge($age),
            static fn (CustomerEntity $customerEntity): CustomerEntity => $customerEntity->setIsAllowedToDrink(),
        );

        self::assertTrue($customerEntity->isAllowedToDrink());
    }

    public function testComplexPipelinesCallingOrder(): void
    {
        // Tests array with nested negative condition.
        $processor = new ConditionalProcessor(
            new Pipeline(
                new ConditionalProcessor(
                    App::$container->get(PipelineInterface::class)->pipe(static fn (array $stack): bool => false)
                ),
                // doesn't change array
                static fn (array $stack): array => array_merge($stack, [1]),
            )
        );

        $stack = $processor(
            [],
            static fn (array $stack): array => array_merge($stack, [0]),
            static fn (array $stack): array => array_merge($stack, [1]),
        );

        foreach ($stack as $index => $value) {
            self::assertSame($index, $value);
        }

        // Tests array with nested positive condition.
        $processor = new ConditionalProcessor(
            new Pipeline(
                new ConditionalProcessor(
                    App::$container->get(PipelineInterface::class)->pipe(static fn (array $stack): bool => true)
                ),
                // doesn't change array even condition always true, because this pipeline is condition
                static fn (array $stack): array => array_merge($stack, [1]),
            )
        );

        $stack = $processor(
            [],
            static fn (array $stack): array => array_merge($stack, [0]),
            static fn (array $stack): array => array_merge($stack, [1]),
        );

        foreach ($stack as $index => $value) {
            self::assertSame($index, $value);
        }

        // Tests object with nested negative condition.
        $processor = new ConditionalProcessor(
            new Pipeline(
                new ConditionalProcessor(
                    App::$container->get(PipelineInterface::class)->pipe(
                        static fn (SimpleArrayWrapper $obj): bool => false
                    )
                ),
                // Run twice.
                static fn (SimpleArrayWrapper $obj): SimpleArrayWrapper => $obj->add(1),
            )
        );

        $obj = $processor(
            new SimpleArrayWrapper(),
            static fn (SimpleArrayWrapper $obj): SimpleArrayWrapper => $obj->add(0),
            static fn (SimpleArrayWrapper $obj): SimpleArrayWrapper => $obj->add(2),
        );

        foreach ($obj->stack as $index => $value) {
            if (3 === $index) {
                self::assertSame(1, $value);
            } else {
                self::assertSame($index, $value);
            }
        }

        // Test object with nested positive condition.
        $processor = new ConditionalProcessor(
            new Pipeline(
                new ConditionalProcessor(
                    App::$container->get(PipelineInterface::class)->pipe(
                        static fn (SimpleArrayWrapper $obj): bool => false
                    )
                ),
                // Run twice.
                static fn (SimpleArrayWrapper $obj): SimpleArrayWrapper => $obj->add(1),
                // Doesn't run.
                static fn (SimpleArrayWrapper $obj): SimpleArrayWrapper => $obj->add(13435353),
            )
        );

        $obj = $processor(
            new SimpleArrayWrapper(),
            static fn (SimpleArrayWrapper $obj): SimpleArrayWrapper => $obj->add(0),
            static fn (SimpleArrayWrapper $obj): SimpleArrayWrapper => $obj->add(2),
        );

        foreach ($obj->get() as $index => $value) {
            if (3 === $index) {
                self::assertSame(1, $value);
            } else {
                self::assertSame($index, $value);
            }
        }

        $processor = new ConditionalProcessor(
            new Pipeline(
                new ConditionalProcessor(
                    App::$container->get(PipelineInterface::class)->pipe(
                        static fn (SimpleArrayWrapper $obj): bool => true
                    )
                ),
                // Run twice.
                static fn (SimpleArrayWrapper $obj): SimpleArrayWrapper => $obj->add(1),
                static fn (SimpleArrayWrapper $obj): SimpleArrayWrapper => $obj->add(2),
            )
        );

        $obj = $processor(
            new SimpleArrayWrapper(),
            static fn (SimpleArrayWrapper $obj): SimpleArrayWrapper => $obj->add(0),
            static fn (SimpleArrayWrapper $obj): SimpleArrayWrapper => $obj->add(3),
        );

        foreach ($obj->get() as $index => $value) {
            match ($index) {
                4 => self::assertSame(1, $value),
                5 => self::assertSame(2, $value),
                default => self::assertSame($index, $value),
            };
        }
    }
}
