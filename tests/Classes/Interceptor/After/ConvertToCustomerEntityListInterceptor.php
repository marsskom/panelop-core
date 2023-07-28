<?php

declare(strict_types=1);

namespace Panelop\Core\Tests\Classes\Interceptor\After;

use Panelop\Core\Interceptor\Interfaces\InterceptorAfterInterface;

use Panelop\Core\Tests\Classes\Customer\CustomerEntity;

use function is_array;

final class ConvertToCustomerEntityListInterceptor implements InterceptorAfterInterface
{
    /**
     * @param mixed|null $payload
     *
     * @return CustomerEntity[]
     */
    public function __invoke(mixed $payload = null): array
    {
        if (!is_array($payload)) {
            return $payload;
        }

        $result = [];
        foreach ($payload as $item) {
            $result[] = new CustomerEntity(...$item);
        }

        return $result;
    }
}
