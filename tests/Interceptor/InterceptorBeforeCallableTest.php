<?php

declare(strict_types=1);

namespace Panelop\Core\Tests\Interceptor;

use LogicException;
use Panelop\Core\Interceptor\Builders\InterceptorBuilder;
use Panelop\Core\Interceptor\Factories\InvocationMethodFactory;
use Panelop\Core\Interceptor\Interfaces\InterceptorBeforeInterface;
use Panelop\Core\Interceptor\Interfaces\InvocationMethodInterface;
use Panelop\Core\Interceptor\InvocationMethod;
use Panelop\Core\Interceptor\InvocationParameter;
use Panelop\Core\Tests\App;
use Panelop\Core\Tests\Classes\Interceptor\Before\ArrayOfMessBeforeWrapper;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use TypeError;

use function count;

#[CoversClass(InterceptorBeforeInterface::class)]
#[CoversClass(InvocationMethodFactory::class)]
#[CoversClass(InvocationMethod::class)]
#[CoversClass(InvocationParameter::class)]
#[CoversClass(InterceptorBuilder::class)]
final class InterceptorBeforeCallableTest extends TestCase
{
    public function testCallable(): void
    {
        $sum = static fn (int|float $first, int|float $second): int|float => $first + $second;

        self::assertEquals(5, $sum(2, 3));
        self::assertEquals(5.9, $sum(2.2, 3.7));

        $interceptor = (new InterceptorBuilder())
            ->before(
                new class () implements InterceptorBeforeInterface {
                    public function __invoke(InvocationMethodInterface $invocationMethod): InvocationMethodInterface
                    {
                        $parameter = $invocationMethod->getParameter('first');
                        $newParameter = (new InvocationMethodFactory())
                            ->overrideInvocationParameterValue(
                                $parameter,
                                $parameter->isActive(),
                                $parameter->getValue() + 1
                            );

                        $invocationMethod->setParameter($newParameter);

                        return $invocationMethod;
                    }
                }
            )
            ->on((new InvocationMethodFactory())->create($sum))
            ->build();

        self::assertEquals(6, $interceptor(2, 3));
        self::assertEquals(6.9, $interceptor(2.2, 3.7));
    }

    public function testExceptionOnChangeParameterType(): void
    {
        $this->expectException(TypeError::class);

        $sumInt = static fn (int $first, int $second): int => $first + $second;

        self::assertEquals(5, $sumInt(2, 3));

        $interceptor = (new InterceptorBuilder())
            ->before(
                new class () implements InterceptorBeforeInterface {
                    public function __invoke(InvocationMethodInterface $invocationMethod): InvocationMethodInterface
                    {
                        $parameter = $invocationMethod->getParameter('first');
                        $newParameter = (new InvocationMethodFactory())
                            ->overrideInvocationParameterValue(
                                $parameter,
                                "We change parameter's value here"
                            );

                        $invocationMethod->setParameter($newParameter);

                        return $invocationMethod;
                    }
                }
            )
            ->on((new InvocationMethodFactory())->create($sumInt))
            ->build();

        self::assertEquals(5, $interceptor(2, 3));
    }

    public static function simpleDataProvider(): iterable
    {
        yield "Simple Positive Integers" => [
            'values' => [77, 12],
            'exception' => '',
            'exceptionMessage' => '',
        ];

        yield "Simple Positive Floats" => [
            'values' => [12.4, 5.7, 90.0009],
            'exception' => '',
            'exceptionMessage' => '',
        ];

        yield "Simple Integers With Negative One" => [
            'values' => [77, -12],
            'exception' => LogicException::class,
            'exceptionMessage' => "Value is negative.",
        ];

        yield "Simple Floats With Negative One" => [
            'values' => [4.5, -1.1, 78.8],
            'exception' => LogicException::class,
            'exceptionMessage' => "Value is negative.",
        ];
    }

    #[DataProvider('simpleDataProvider')]
    public function testAttributeWithoutDi(
        array $values,
    ): void {
        $arrayOfMess = new ArrayOfMessBeforeWrapper();

        foreach ($values as $value) {
            $arrayOfMess = $arrayOfMess->add($value);
        }

        self::assertCount(count($values), $arrayOfMess->get());

        foreach ($values as $value) {
            self::assertContains($value, $arrayOfMess->get());
        }
    }

    #[DataProvider('simpleDataProvider')]
    public function testAttributeWithDi(
        array  $values,
        string $exception,
        string $exceptionMessage,
    ): void {
        if (!empty($exception) && !empty($exceptionMessage)) {
            $this->expectException($exception);
            $this->expectExceptionMessage($exceptionMessage);
        }

        $arrayOfMess = App::$container->get(ArrayOfMessBeforeWrapper::class);

        foreach ($values as $value) {
            $arrayOfMess = $arrayOfMess->add($value);
        }

        self::assertCount(count($values), $arrayOfMess->get());

        foreach ($values as $value) {
            self::assertContains($value, $arrayOfMess->get());
        }
    }

    public static function simpleDataProviderForPositiveInt(): iterable
    {
        yield "Simple Positive Integers" => [
            'values' => [77, 12],
            'exception' => '',
            'exceptionMessage' => '',
        ];

        yield "Simple Positive Floats" => [
            'values' => [12.4, 5.7, 90.0009],
            'exception' => LogicException::class,
            'exceptionMessage' => "Value is not int.",
        ];

        yield "Simple Integers With Negative One" => [
            'values' => [77, -12],
            'exception' => LogicException::class,
            'exceptionMessage' => "Value is negative.",
        ];

        yield "Simple Floats With Negative One" => [
            'values' => [4.5, -1.1, 78.8],
            'exception' => LogicException::class,
            'exceptionMessage' => "Value is not int.",
        ];

        yield "Complex Problems But Negative Is First" => [
            'values' => [5, -1, 78.8],
            'exception' => LogicException::class,
            'exceptionMessage' => "Value is negative.",
        ];

        yield "Complex Problems But Float Is First" => [
            'values' => [67, 5.8, -4],
            'exception' => LogicException::class,
            'exceptionMessage' => "Value is not int.",
        ];
    }

    #[DataProvider('simpleDataProviderForPositiveInt')]
    public function testAttributeWithDiOnlyPositiveInt(
        array  $values,
        string $exception,
        string $exceptionMessage,
    ): void {
        if (!empty($exception) && !empty($exceptionMessage)) {
            $this->expectException($exception);
            $this->expectExceptionMessage($exceptionMessage);
        }

        $arrayOfMess = App::$container->get(ArrayOfMessBeforeWrapper::class);

        foreach ($values as $value) {
            $arrayOfMess = $arrayOfMess->addOnlyPositiveInt($value);
        }

        self::assertCount(count($values), $arrayOfMess->get());

        foreach ($values as $value) {
            self::assertContains($value, $arrayOfMess->get());
        }
    }
}
