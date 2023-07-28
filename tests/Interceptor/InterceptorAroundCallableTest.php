<?php

declare(strict_types=1);

namespace Panelop\Core\Tests\Interceptor;

use Panelop\Core\Interceptor\Builders\InterceptorBuilder;
use Panelop\Core\Interceptor\Factories\InvocationMethodFactory;
use Panelop\Core\Interceptor\Interfaces\InterceptorAroundInterface;
use Panelop\Core\Interceptor\Interfaces\InvocationAroundResultInterface;
use Panelop\Core\Interceptor\InvocationAroundResult;
use Panelop\Core\Interceptor\InvocationMethod;
use Panelop\Core\Interceptor\InvocationParameter;
use Panelop\Core\Tests\App;
use Panelop\Core\Tests\Classes\Interceptor\Around\ArrayOfMessAroundWrapper;
use Panelop\Core\Tests\Classes\Interceptor\Around\ParametersLogInterceptor;
use Panelop\Core\Tests\DataProviders\Interceptor\InterceptorAroundDataProvider;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProviderExternal;
use PHPUnit\Framework\TestCase;

use function array_values;
use function call_user_func_array;
use function is_object;

#[CoversClass(InterceptorAroundInterface::class)]
#[CoversClass(InvocationMethodFactory::class)]
#[CoversClass(InvocationMethod::class)]
#[CoversClass(InvocationParameter::class)]
#[CoversClass(InterceptorBuilder::class)]
final class InterceptorAroundCallableTest extends TestCase
{
    #[DataProviderExternal(EmptyInterceptorTest::class, 'dataProvider')]
    public function testCallable(
        callable $callable,
        bool     $hasVariadic,
    ): void {
        self::assertSame(['Hello', 'world!'], $callable('Hello', 'world!'));
        self::assertSame([1, 2], $callable(1, 2));

        if ($hasVariadic) {
            self::assertSame([1, 2, 3, 4, 5], $callable(1, 2, 3, 4, 5));
        }

        $interceptor = (new InterceptorBuilder())
            ->around(
                new class () implements InterceptorAroundInterface {
                    public function __invoke(
                        InvocationAroundResultInterface $invocationAroundResult
                    ): InvocationAroundResultInterface {
                        // Doesn't use original callable.
                        $payload = $invocationAroundResult->getPayload();
                        unset($payload[0]);

                        return new InvocationAroundResult(
                            $invocationAroundResult->getInvocationMethod(),
                            array_values($payload)
                        );
                    }
                }
            )
            ->on((new InvocationMethodFactory())->create($callable))
            ->build();

        self::assertSame(['world!'], $interceptor('Hello', 'world!'));
        self::assertSame([2], $interceptor(1, 2));

        if ($hasVariadic) {
            self::assertSame([2, 3, 4, 5], $interceptor(1, 2, 3, 4, 5));
        }


        $interceptor = (new InterceptorBuilder())
            ->around(
                new class () implements InterceptorAroundInterface {
                    public function __invoke(
                        InvocationAroundResultInterface $invocationAroundResult
                    ): InvocationAroundResultInterface {
                        // Uses callable.
                        $payload = $invocationAroundResult->getInvocationMethod()->proceed(
                            ...$invocationAroundResult->getPayload()
                        );

                        $payload[1] = "-- it is the result --";

                        return new InvocationAroundResult(
                            $invocationAroundResult->getInvocationMethod(),
                            $payload
                        );
                    }
                }
            )
            ->on((new InvocationMethodFactory())->create($callable))
            ->build();

        self::assertSame(['Hello', "-- it is the result --"], $interceptor('Hello', 'world!'));
        self::assertSame([1, "-- it is the result --"], $interceptor(1, 2));

        if ($hasVariadic) {
            self::assertSame([1, "-- it is the result --", 3, 4, 5], $interceptor(1, 2, 3, 4, 5));
        }
    }

    #[DataProviderExternal(InterceptorAroundDataProvider::class, 'dataProvider')]
    public function testLogOnMess(
        array $commands,
        array $logDataExpected,
    ): void {
        /**
         * @var ParametersLogInterceptor $logger
         * @var ArrayOfMessAroundWrapper $mess
         */
        // Inits logger for saving data here. It works, since `$container->get` returns same instance if it exists.
        $logger = App::$container->get(ParametersLogInterceptor::class);
        $logger->logs = [];
        $mess = App::$container->get(ArrayOfMessAroundWrapper::class);

        foreach ($commands as [0 => $method, 1 => $arguments]) {
            $result = call_user_func_array([$mess, $method], $arguments);

            if (is_object($result)) {
                $mess = $result;
            }
        }

        self::assertSame($logDataExpected, $logger->logs);
    }
}
