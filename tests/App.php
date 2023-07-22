<?php

declare(strict_types=1);

namespace Panelop\Core\Tests;

use Psr\Container\ContainerInterface;

final class App
{
    public static ContainerInterface $container;

    public function __construct(
        ContainerInterface $container,
    ) {
        self::$container = $container;
    }
}
