<?php

declare(strict_types=1);

namespace Panelop\Core\Interceptor\Exceptions;

use Exception;
use Throwable;

use function sprintf;

final class InvocationParameterNotFoundException extends Exception
{
    public function __construct(
        string     $message = "Invocation parameter not found.",
        int        $code = 0,
        ?Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }

    public static function create(string $parameterName): InvocationParameterNotFoundException
    {
        return new InvocationParameterNotFoundException(
            sprintf("Invocation parameter '%s' not found.", $parameterName)
        );
    }
}
