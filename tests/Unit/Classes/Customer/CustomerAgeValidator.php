<?php

declare(strict_types=1);

namespace Panelop\Core\Tests\Unit\Classes\Customer;

use InvalidArgumentException;

final class CustomerAgeValidator
{
    public function validateValue(CustomerEntity $customerEntity): CustomerEntity
    {
        if (0 >= $customerEntity->getAge()) {
            throw new InvalidArgumentException("Age is invalid");
        }

        return $customerEntity;
    }

    public function validateIsAdult(CustomerEntity $customerEntity): CustomerEntity
    {
        if (21 > $customerEntity->getAge()) {
            throw new InvalidArgumentException("Isn't adult");
        }

        return $customerEntity;
    }
}
