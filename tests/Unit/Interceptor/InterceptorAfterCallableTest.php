<?php

declare(strict_types=1);

namespace Panelop\Core\Tests\Unit\Interceptor;

use Panelop\Core\Interceptor\Attributes\InterceptorsAfter;
use Panelop\Core\Interceptor\Builders\InterceptorBuilder;
use Panelop\Core\Interceptor\Callables\InvocationMethodArgumentsFillCallable;
use Panelop\Core\Interceptor\Factories\InvocationMethodFactory;
use Panelop\Core\Interceptor\Interfaces\InterceptorAfterInterface;
use Panelop\Core\Interceptor\InvocationInterceptor;
use Panelop\Core\Interceptor\InvocationMethod;
use Panelop\Core\Interceptor\InvocationParameter;
use Panelop\Core\Interceptor\Pipelines\InterceptorProcessor;
use Panelop\Core\Tests\App;
use Panelop\Core\Tests\Unit\Classes\Customer\CustomerEntity;
use Panelop\Core\Tests\Unit\Classes\Interceptor\After\ArrayOfMessAfterWrapper;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\DataProviderExternal;
use PHPUnit\Framework\TestCase;

use function implode;

#[CoversClass(InterceptorsAfter::class)]
#[CoversClass(InterceptorAfterInterface::class)]
#[CoversClass(InvocationMethodFactory::class)]
#[CoversClass(InvocationMethod::class)]
#[CoversClass(InvocationParameter::class)]
#[CoversClass(InterceptorBuilder::class)]
#[CoversClass(InvocationInterceptor::class)]
#[CoversClass(InterceptorProcessor::class)]
#[CoversClass(InvocationMethodArgumentsFillCallable::class)]
final class InterceptorAfterCallableTest extends TestCase
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
            ->after(
                new class () implements InterceptorAfterInterface {
                    public function __invoke(mixed $payload = null): string
                    {
                        return implode(' | ', $payload ?? []);
                    }
                }
            )
            ->on((new InvocationMethodFactory())->create($callable))
            ->build();

        self::assertSame('Hello | world!', $interceptor('Hello', 'world!'));
        self::assertSame('1 | 2', $interceptor(1, 2));

        if ($hasVariadic) {
            self::assertSame('1 | 2 | 3 | 4 | 5', $interceptor(1, 2, 3, 4, 5));
        }
    }

    public static function dataProvider(): iterable
    {
        yield "Customer Data" => [
            'customerData' => [
                ['age' => 2, 'isAllowedToDrink' => false],
                ['age' => 22, 'isAllowedToDrink' => true],
                ['age' => 56, 'isAllowedToDrink' => true],
                ['age' => 18, 'isAllowedToDrink' => false],
                ['age' => 34, 'isAllowedToDrink' => true],
                ['age' => 14, 'isAllowedToDrink' => false],
                ['age' => 8, 'isAllowedToDrink' => false],
                ['age' => 17, 'isAllowedToDrink' => false],
                ['age' => 28, 'isAllowedToDrink' => true],
                ['age' => 13, 'isAllowedToDrink' => false],
                ['age' => 67, 'isAllowedToDrink' => true],
                ['age' => 8, 'isAllowedToDrink' => false],
                ['age' => 10, 'isAllowedToDrink' => false],
                ['age' => 2, 'isAllowedToDrink' => true],
            ],
        ];
    }

    #[DataProvider('dataProvider')]
    public function testModifyClass(array $customerData): void
    {
        /** @var ArrayOfMessAfterWrapper $mess */
        $mess = App::$container->get(ArrayOfMessAfterWrapper::class);

        foreach ($customerData as $customer) {
            $mess = $mess->add($customer);
        }

        $customerEntityList = $mess->get();
        self::assertSameSize($customerData, $customerEntityList);

        $fromEntityDataArray = [];
        foreach ($customerEntityList as $entity) {
            self::assertInstanceOf(CustomerEntity::class, $entity);

            $fromEntityDataArray[] = ['age' => $entity->getAge(), 'isAllowedToDrink' => $entity->isAllowedToDrink()];
        }


        self::assertSame($customerData, $fromEntityDataArray);
    }
}
