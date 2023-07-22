<?php

declare(strict_types=1);

use DI\ContainerBuilder;
use Panelop\Core\Tests\App;

define('PROJECT_ROOT', dirname(__DIR__));

require_once PROJECT_ROOT . '/vendor/autoload.php';
require_once __DIR__ . '/App.php';

$builder = new ContainerBuilder();
$builder->enableCompilation(PROJECT_ROOT . '/runtime/tests/di');
$builder->writeProxiesToFile(true, PROJECT_ROOT . '/runtime/tests/di/proxies');
$builder->addDefinitions(__DIR__ . '/config.php');

new App($builder->build());
