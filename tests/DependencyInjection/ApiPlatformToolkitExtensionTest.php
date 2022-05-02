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

namespace CyberSpectrum\ApiPlatformToolkit\Tests\DependencyInjection;

use CyberSpectrum\ApiPlatformToolkit\DependencyInjection\ApiPlatformToolkitExtension;
use CyberSpectrum\ApiPlatformToolkit\EventListener\AddAudToJwtListener;
use CyberSpectrum\ApiPlatformToolkit\EventListener\OverrideJwtTtlListener;
use CyberSpectrum\ApiPlatformToolkit\Serializer\Normalizer\AddApiLoginNormalizer;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Test.
 *
 * @covers \CyberSpectrum\ApiPlatformToolkit\DependencyInjection\ApiPlatformToolkitExtension
 */
class ApiPlatformToolkitExtensionTest extends TestCase
{
    /** Test that the service.yml is loaded with default values. */
    public function testLoadWithDefaults(): void
    {
        $container = new ContainerBuilder();
        $extension = new ApiPlatformToolkitExtension();

        $extension->load([], $container);

        self::assertTrue($container->getParameter('csap_toolkit.enable_expression_language'));
        self::assertSame(3600, $container->getParameter('csap_toolkit.lexik_jwt_default_ttl'));
        self::assertSame('/api/login_check', $container->getParameter('csap_toolkit.lexik_jwt_login_url'));
        self::assertTrue($container->has(OverrideJwtTtlListener::class));
        self::assertTrue($container->has(AddAudToJwtListener::class));
        self::assertTrue($container->has(AddApiLoginNormalizer::class));
    }

    /** Test that the documentation service may be disabled. */
    public function testDisableJwtDocumentation(): void
    {
        $container = new ContainerBuilder();
        $extension = new ApiPlatformToolkitExtension();

        $extension->load([
            'api_platform_toolkit' => [
                'lexik_jwt' => [
                    'add_documentation' => false,
                ]
            ]
        ], $container);

        self::assertFalse($container->has(AddApiLoginNormalizer::class));
    }

    /** Test that the documentation service may be disabled. */
    public function testDisableJwt(): void
    {
        $container = new ContainerBuilder();
        $extension = new ApiPlatformToolkitExtension();

        $extension->load([
            'api_platform_toolkit' => [
                'lexik_jwt' => false
            ]
        ], $container);

        self::assertFalse($container->has(AddApiLoginNormalizer::class));
    }

    /** Test that the documentation service may be disabled. */
    public function testDisableAddAudToJwt(): void
    {
        $container = new ContainerBuilder();
        $extension = new ApiPlatformToolkitExtension();

        $extension->load([
            'api_platform_toolkit' => [
                'lexik_jwt' => [
                    'add_aud' => false,
                ]
            ]
        ], $container);

        self::assertFalse($container->has(AddAudToJwtListener::class));
    }

    /** Test that the service.yml is loaded. */
    public function testDisableExpressionLanguage(): void
    {
        $container = new ContainerBuilder();
        $extension = new ApiPlatformToolkitExtension();

        $extension->load([
            'api_platform_toolkit' => [
                'enable_expression_language' => false,
            ]
        ], $container);

        self::assertTrue($container->has(OverrideJwtTtlListener::class));
    }
}
