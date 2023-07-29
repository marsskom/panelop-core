<?php

declare(strict_types=1);

namespace Panelop\Core\Tests\DataProviders\Interceptor;

use function base64_encode;
use function random_bytes;
use function str_replace;
use function substr;

final class RequestHeaderDataProvider
{
    public static function fewAmountDataProvider(): iterable
    {
        for ($i = 0; $i < 10; ++$i) {
            yield "Step #$i" => [
                'headers' => self::generateHeaders(),
            ];
        }
    }

    public static function largeAmountDataProvider(): iterable
    {
        for ($i = 0; $i < 1000; ++$i) {
            yield "Step #$i" => [
                'headers' => self::generateHeaders(),
            ];
        }
    }

    private static function generateHeaders(): array
    {
        $headers = [
            "Host: www.example.com",
            "User-Agent: Mozilla/5.0 (X11; Linux x86_64; rv:109.0) Gecko/20100101 Firefox/115.0",
            "Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,*/*;q=0.8",
            "Accept-Language: en-US,en;q=0.5",
            "Accept-Encoding: gzip, deflate, br",
            "Connection: keep-alive",
        ];

        $flag = mt_rand() / mt_getrandmax();
        if ($flag <= 0.5) {
            return $headers;
        }

        return [
            ...$headers,
            "Cookie: PHPSESSID=" . substr(str_replace(['+', '/', '='], '', base64_encode(random_bytes(26))), 0, 32),
            "Upgrade-Insecure-Requests: 1",
            "Sec-Fetch-Dest: document",
            "Sec-Fetch-Mode: navigate",
            "Sec-Fetch-Site: cross-site",
            "Sec-GPC: 1",
            "DNT: 1",
            "If-Modified-Since: Tue, 18 Jul 2023 18:24:28 GMT",
        ];
    }
}
