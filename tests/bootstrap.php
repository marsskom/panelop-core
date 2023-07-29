<?php

declare(strict_types=1);

use DI\ContainerBuilder;
use Panelop\Core\Tests\App;
use Panelop\Core\Tests\DI\InterceptorContainer;

define('PROJECT_ROOT', dirname(__DIR__));

require_once PROJECT_ROOT . '/vendor/autoload.php';
require_once __DIR__ . '/App.php';

$builder = new ContainerBuilder(InterceptorContainer::class);
$builder->addDefinitions(__DIR__ . '/config.php');

new App($builder->build());

if (!function_exists('http_parse_headers')) {
    /**
     * @param string[] $headers
     *
     * @return array<string, string>
     */
    function http_parse_headers(array $headers): array
    {
        $output = [];

        foreach ($headers as $header) {
            $chunks = preg_split('/:\s*/', $header, 2);
            $output[strtolower($chunks[0])] = $chunks[1];
        }

        return $output;
    }
}
