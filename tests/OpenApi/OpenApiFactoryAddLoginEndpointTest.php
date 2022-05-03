<?php

/**
 * (c) 2019 Christian Schiffler.
 *
 * This project is provided in good faith and hope to be usable by anyone.
 *
 * @package    cyberspectrum/api-platform-toolkit-bundle
 * @author     Christian Schiffler <c.schiffler@cyberspectrum.de>
 * @copyright  2019 Christian Schiffler.
 * @license    https://www.cyberspectrum.de/ MIT
 * @filesource
 */

declare(strict_types=1);

namespace CyberSpectrum\ApiPlatformToolkit\Tests\OpenApi;

use ApiPlatform\Core\OpenApi\Factory\OpenApiFactoryInterface;
use ApiPlatform\Core\OpenApi\Model\Components;
use ApiPlatform\Core\OpenApi\Model\Info;
use ApiPlatform\Core\OpenApi\Model\PathItem;
use ApiPlatform\Core\OpenApi\Model\Paths;
use ApiPlatform\Core\OpenApi\OpenApi;
use CyberSpectrum\ApiPlatformToolkit\OpenApi\OpenApiFactoryAddLoginEndpoint;
use PHPUnit\Framework\TestCase;

/** @covers \CyberSpectrum\ApiPlatformToolkit\OpenApi\OpenApiFactoryAddLoginEndpoint */
final class OpenApiFactoryAddLoginEndpointTest extends TestCase
{
    public function testAddsLoginUri(): void
    {
        $baseOpenApi = new OpenApi(
            new Info('API title', '3.0'),
            [],
            new Paths(),
            new Components()
        );

        $previous = $this->getMockForAbstractClass(OpenApiFactoryInterface::class);
        $previous->expects(self::once())->method('__invoke')->willReturn($baseOpenApi);

        $processor = new OpenApiFactoryAddLoginEndpoint(
            $previous,
            [
                'json' => ['application/json'],
                'csv' => ['text/csv']
            ],
            '/api/login',
            null,
            7200,
        );

        $openApi = $processor->__invoke();

        self::assertInstanceOf(PathItem::class, $openApi->getPaths()->getPath('/api/login'));
        self::assertNull($openApi->getPaths()->getPath('/api/login_refresh'));
    }
}
