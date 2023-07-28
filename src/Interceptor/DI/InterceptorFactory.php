<?php

declare(strict_types=1);

namespace Panelop\Core\Interceptor\DI;

use Panelop\Core\Interceptor\Attributes\InterceptorsAfter;
use Panelop\Core\Interceptor\Attributes\InterceptorsAround;
use Panelop\Core\Interceptor\Attributes\InterceptorsBefore;
use Panelop\Core\Interceptor\Builders\InterceptorBuilder;
use Panelop\Core\Interceptor\Factories\InvocationMethodFactory;
use Psr\Container\ContainerInterface;
use ReflectionClass;

use function array_map;
use function is_object;

/**
 * The factory is just an example. You can and should use your DI capabilities: factories, proxy etc., for interceptor creation.
 */
final class InterceptorFactory
{
    private ?ReflectionClass $reflection = null;

    public function create(ContainerInterface $container, mixed $containerResult): mixed
    {
        if (!$this->validate($containerResult)) {
            return $containerResult;
        }

        $calls = [];
        $this->reflection ??= new ReflectionClass($containerResult);
        foreach ($this->reflection->getMethods() as $method) {
            $interceptorBuilder = new InterceptorBuilder();

            foreach ($method->getAttributes() as $attribute) {
                match ($attribute->getName()) {
                    InterceptorsBefore::class => $interceptorBuilder->before(
                        ...$this->createInterceptors($container, ...$attribute->newInstance()->interceptors)
                    ),
                    InterceptorsAround::class => $interceptorBuilder->around(
                        ...$this->createInterceptors($container, ...$attribute->newInstance()->interceptors)
                    ),
                    InterceptorsAfter::class => $interceptorBuilder->after(
                        ...$this->createInterceptors($container, ...$attribute->newInstance()->interceptors)
                    ),
                    default => null,
                };
            }

            $calls[$method->getName()] =
                $interceptorBuilder->on(
                    (new InvocationMethodFactory())->create([
                        &$containerResult, $method->getName(),
                    ])
                )->build();
        }

        return (new Interceptor($this->reflection->getName(), $calls));
    }

    private function validate(mixed $containerResult): bool
    {
        if (!is_object($containerResult)) {
            return false;
        }

        if ($containerResult instanceof InterceptableClassInterface) {
            return true;
        }

        $this->reflection = new ReflectionClass($containerResult);

        return !empty($this->reflection->getAttributes(InterceptableClass::class));
    }

    private function createInterceptors(ContainerInterface $container, string ...$interceptors): array
    {
        return array_map(
            static fn (string $interceptor): callable => $container->get($interceptor),
            $interceptors
        );
    }
}
