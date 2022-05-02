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
use ApiPlatform\Core\OpenApi\Model\Info;
use ApiPlatform\Core\OpenApi\Model\Operation;
use ApiPlatform\Core\OpenApi\Model\PathItem;
use ApiPlatform\Core\OpenApi\Model\Paths;
use ApiPlatform\Core\OpenApi\Model\RequestBody;
use ApiPlatform\Core\OpenApi\Model\Response;
use ApiPlatform\Core\OpenApi\OpenApi;
use ArrayObject;
use CyberSpectrum\ApiPlatformToolkit\OpenApi\OpenApiFactoryRemoveHtmlFormat;
use PHPUnit\Framework\TestCase;

/** @covers \CyberSpectrum\ApiPlatformToolkit\OpenApi\OpenApiFactoryRemoveHtmlFormat */
final class OpenApiFactoryRemoveHtmlFormatTest extends TestCase
{
    /** @SuppressWarnings(PHPMD.ExcessiveMethodLength) */
    public function testRemovesPayload(): void
    {
        $baseOpenApi = new OpenApi(
            new Info('API title', '3.0'),
            [],
            $paths = new Paths()
        );

        $paths->addPath(
            '/test',
            new PathItem(
                null,
                null,
                null,
                new Operation(
                    'get',
                    [],
                    [
                        '200' => new Response(
                            '',
                            new ArrayObject([
                                'application/ld+json' => ['$ref' => '#/components/schemas/Dummy'],
                                'application/json' => ['$ref' => '#/components/schemas/Dummy'],
                                'text/csv' => ['$ref' => '#/components/schemas/Dummy'],
                                'text/html' => ['$ref' => '#/components/schemas/Dummy'],
                            ])
                        )
                    ],
                    '',
                    '',
                    null,
                    [],
                    null
                ),
                null,
                new Operation(
                    'post',
                    [],
                    [
                        '200' => new Response(
                            '',
                            new ArrayObject([
                                'application/ld+json' => ['$ref' => '#/components/schemas/Dummy'],
                                'application/json' => ['$ref' => '#/components/schemas/Dummy'],
                                'text/csv' => ['$ref' => '#/components/schemas/Dummy'],
                                'text/html' => ['$ref' => '#/components/schemas/Dummy'],
                            ])
                        )
                    ],
                    '',
                    '',
                    null,
                    [],
                    new RequestBody(
                        '',
                        new ArrayObject([
                            'application/ld+json' => ['$ref' => '#/components/schemas/DummyRequest'],
                            'application/json' => ['$ref' => '#/components/schemas/DummyRequest'],
                            'text/csv' => ['$ref' => '#/components/schemas/DummyRequest'],
                            'text/html' => ['$ref' => '#/components/schemas/DummyRequest'],
                        ]),
                        true
                    )
                ),
                new Operation(
                    'delete',
                    [],
                    [
                        '200' => new Response()
                    ],
                    '',
                    '',
                    null,
                    [],
                    new RequestBody(
                        '',
                        new ArrayObject(),
                        true
                    )
                ),
            )
        );

        $previous = $this->getMockForAbstractClass(OpenApiFactoryInterface::class);
        $previous->expects(self::once())->method('__invoke')->willReturn($baseOpenApi);

        $remover = new OpenApiFactoryRemoveHtmlFormat($previous);

        $openApi = $remover->__invoke();

        foreach ($openApi->getPaths()->getPaths() as $pathName => $path) {
            foreach (
                [
                    'getGet',
                    'getPut',
                    'getPost',
                    'getDelete',
                    'getOptions',
                    'getHead',
                    'getPatch',
                    'getTrace',
                ] as $methodName
            ) {
                $operation = call_user_func([$path, $methodName]);
                if (null === $operation) {
                    continue;
                }
                assert($operation instanceof Operation);
                $prefix = 'Operation ' . $pathName . ' method ' . $methodName;

                $responses = $operation->getResponses();
                foreach ($responses as $response) {
                    $content = $response->getContent();
                    if (null === $content || 0 === $content->count()) {
                        continue;
                    }

                    self::assertArrayNotHasKey('text/html', $content, $prefix . ' not cleaned!');
                    self::assertArrayHasKey('application/ld+json', $content, $prefix . ' cleaned!');
                    self::assertArrayHasKey('application/json', $content, $prefix . ' cleaned!');
                    self::assertArrayHasKey('text/csv', $content, $prefix . ' cleaned!');
                }
                $requestBody = $operation->getRequestBody();
                if (null === $requestBody) {
                    continue;
                }
                $content = $requestBody->getContent();
                if (0 === $content->count()) {
                    continue;
                }

                self::assertArrayNotHasKey('text/html', $content, $prefix . '  not cleaned!');
                self::assertArrayHasKey('application/ld+json', $content, $prefix . ' cleaned!');
                self::assertArrayHasKey('application/json', $content, $prefix . ' cleaned!');
                self::assertArrayHasKey('text/csv', $content, $prefix . ' cleaned!');
            }
        }
    }
}
