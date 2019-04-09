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

declare(strict_types = 1);

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
    /**
     * Test that the service.yml is loaded.
     *
     * @return void
     */
    public function testLoadWithDefaults(): void
    {
        $container = new ContainerBuilder();
        $extension = new ApiPlatformToolkitExtension();

        $extension->load([], $container);

        $this->assertTrue($container->getParameter('csap_toolkit.enable_expression_language'));
        $this->assertSame(3600, $container->getParameter('csap_toolkit.lexik_jwt_default_ttl'));
        $this->assertSame('/api/login_check', $container->getParameter('csap_toolkit.lexik_jwt_login_url'));
        $this->assertTrue($container->has(OverrideJwtTtlListener::class));
        $this->assertTrue($container->has(AddAudToJwtListener::class));
        $this->assertTrue($container->has(AddApiLoginNormalizer::class));
    }

    /**
     * Test that the documentation service may be disabled.
     *
     * @return void
     */
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

        $this->assertFalse($container->has(AddApiLoginNormalizer::class));
    }

    /**
     * Test that the documentation service may be disabled.
     *
     * @return void
     */
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

        $this->assertFalse($container->has(AddAudToJwtListener::class));
    }

    /**
     * Test that the service.yml is loaded.
     *
     * @return void
     */
    public function testLoadsServiceYml(): void
    {
        $container = new ContainerBuilder();
        $extension = new ApiPlatformToolkitExtension();

        $extension->load([], $container);

        $this->assertTrue($container->has(OverrideJwtTtlListener::class));
    }
}
