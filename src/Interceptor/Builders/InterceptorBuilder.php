<?php

declare(strict_types=1);

namespace Panelop\Core\Interceptor\Builders;

use Panelop\Core\Interceptor\Callables\InvocationMethodArgumentsFillCallable;
use Panelop\Core\Interceptor\Interfaces\InvocationAroundResultInterface;
use Panelop\Core\Interceptor\Interfaces\InvocationInterceptorInterface;
use Panelop\Core\Interceptor\Interfaces\InvocationMethodInterface;
use Panelop\Core\Interceptor\InvocationAroundResult;
use Panelop\Core\Interceptor\InvocationInterceptor;
use Panelop\Core\Interceptor\Pipelines\InterceptorProcessor;
use Panelop\Core\Pipeline\DefaultProcessor;
use Panelop\Core\Pipeline\Interfaces\PipelineInterface;
use Panelop\Core\Pipeline\Pipeline;

use function array_shift;

final class InterceptorBuilder
{
    private InvocationMethodInterface $invocationMethod;

    private array $before = [];

    private array $after = [];

    private array $around = [];

    public function before(callable ...$interceptors): self
    {
        $this->before = [
            ...$this->before,
            ...$interceptors,
        ];

        return $this;
    }

    public function around(callable ...$interceptors): self
    {
        $this->around = [
            ...$this->around,
            ...$interceptors,
        ];

        return $this;
    }

    public function after(callable ...$interceptors): self
    {
        $this->after = [
            ...$this->after,
            ...$interceptors,
        ];

        return $this;
    }

    public function on(InvocationMethodInterface $invocationMethod): self
    {
        $this->invocationMethod = $invocationMethod;

        return $this;
    }

    public function build(): InvocationInterceptorInterface
    {
        $pipeline = new Pipeline(
            new InterceptorProcessor($this->getBeforeInterceptorsPipeline())
        );
        $pipeline = $this->addAroundInterceptors($pipeline);

        return new InvocationInterceptor($this->addAfterInterceptors($pipeline));
    }

    private function getBeforeInterceptorsPipeline(): PipelineInterface
    {
        return (new Pipeline(
            new DefaultProcessor(),
            new InvocationMethodArgumentsFillCallable($this->invocationMethod)
        ))->pipe(...$this->before);
    }

    private function addAroundInterceptors(PipelineInterface $pipeline): PipelineInterface
    {
        if (empty($this->around)) {
            return $pipeline->pipe(
                fn(InvocationMethodInterface $invocationMethod): mixed => new InvocationAroundResult(
                    $invocationMethod,
                    $this->invocationMethod->proceed(
                        ...$invocationMethod->getArguments()
                    )
                )
            );
        }

        $firstAroundMethod = array_shift($this->around);

        return $pipeline->pipe(
            static fn(InvocationMethodInterface $invocationMethod): mixed => $firstAroundMethod(
                new InvocationAroundResult($invocationMethod, $invocationMethod->getArguments())
            ),
            ...$this->around
        );
    }

    private function addAfterInterceptors(PipelineInterface $pipeline): PipelineInterface
    {
        if (empty($this->after)) {
            return $pipeline->pipe(
                static fn(
                    InvocationAroundResultInterface $invocationAroundResult
                ): mixed => $invocationAroundResult->getPayload()
            );
        }

        return $pipeline->pipe(
            static fn(
                InvocationAroundResultInterface $invocationAroundResult
            ): mixed => $invocationAroundResult->getPayload(),
            ...$this->after
        );
    }
}
