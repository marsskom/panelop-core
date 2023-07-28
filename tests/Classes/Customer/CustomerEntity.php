<?php

declare(strict_types=1);

namespace Panelop\Core\Tests\Classes\Customer;

final class CustomerEntity
{
    public function __construct(
        private int  $age = 1,
        private bool $isAllowedToDrink = false,
    ) {
    }

    public function setAge(int $age): self
    {
        $this->age = $age;

        return $this;
    }

    public function getAge(): int
    {
        return $this->age;
    }

    public function setIsAllowedToDrink(): self
    {
        $this->isAllowedToDrink = true;

        return $this;
    }

    public function isAllowedToDrink(): bool
    {
        return $this->isAllowedToDrink;
    }
}
