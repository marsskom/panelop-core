<?php

declare(strict_types=1);

namespace Panelop\Core\Interceptor\Exceptions;

use Exception;
use JsonException;
use Throwable;

use function json_encode;
use function sprintf;

final class InvocationMethodCannotBeCreatedException extends Exception
{
    public function __construct(
        string     $message = "Invocation method cannot be created.",
        int        $code = 0,
        ?Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }

    /**
     * @throws JsonException
     */
    public static function create(mixed $callable): InvocationMethodCannotBeCreatedException
    {
        return new InvocationMethodCannotBeCreatedException(
            sprintf(
                "Invocation method cannot be created from '%s', json: '%s'",
                gettype($callable),
                json_encode($callable, JSON_THROW_ON_ERROR)
            )
        );
    }
}
