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

namespace CyberSpectrum\ApiPlatformToolkit\Tests\DependencyInjection\Compiler;

use CyberSpectrum\ApiPlatformToolkit\DependencyInjection\Compiler\AddExpressionLanguageProvidersPass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

/**
 * Test.
 *
 * @covers \CyberSpectrum\ApiPlatformToolkit\DependencyInjection\Compiler\AddExpressionLanguageProvidersPass
 */
class AddExpressionLanguageProvidersPassTest extends TestCase
{
    public function testIgnoresServicesWhenExpressionLanguageNotActivated(): void
    {
        $container = $this
            ->getMockBuilder(ContainerBuilder::class)
            ->onlyMethods(['getParameter', 'has', 'findDefinition'])
            ->getMock();
        $container
            ->expects($this->once())
            ->method('getParameter')
            ->with('csap_toolkit.enable_expression_language')
            ->willReturn(true);
        $container
            ->expects($this->once())
            ->method('has')
            ->with('api_platform.security.expression_language')
            ->willReturn(false);
        $container
            ->expects($this->never())
            ->method('findDefinition')
            ->with('api_platform.security.expression_language');

        $pass = new AddExpressionLanguageProvidersPass();
        $pass->process($container);
    }

    /**
     * Test.
     *
     * @return void
     */
    public function testIgnoresServicesWhenExpressionLanguageHandlingIsDisabled()
    {
        $container = $this
            ->getMockBuilder(ContainerBuilder::class)
            ->onlyMethods(['getParameter', 'has', 'findDefinition'])
            ->getMock();
        $container
            ->expects($this->once())
            ->method('getParameter')
            ->with('csap_toolkit.enable_expression_language')
            ->willReturn(false);
        $container
            ->expects($this->never())
            ->method('has')
            ->with('api_platform.security.expression_language');
        $container
            ->expects($this->never())
            ->method('findDefinition')
            ->with('api_platform.security.expression_language');

        $pass = new AddExpressionLanguageProvidersPass();
        $pass->process($container);
    }

    public function testAddsServices(): void
    {
        $service1 = new Definition();
        $service2 = new Definition();

        $container = $this
            ->getMockBuilder(ContainerBuilder::class)
            ->onlyMethods(['getParameter', 'has', 'findDefinition', 'findTaggedServiceIds'])
            ->getMock();
        $container
            ->expects($this->once())
            ->method('getParameter')
            ->with('csap_toolkit.enable_expression_language')
            ->willReturn(true);
        $container
            ->expects($this->once())
            ->method('has')
            ->with('api_platform.security.expression_language')
            ->willReturn(true);
        $definition = $this->getMockBuilder(Definition::class)->getMock();
        $container
            ->expects($this->once())
            ->method('findDefinition')
            ->with('api_platform.security.expression_language')
            ->willReturn($definition);
        $container
            ->expects($this->once())
            ->method('findTaggedServiceIds')
            ->with('csap_toolkit.security.expression_language_provider')
            ->willReturn(['service1' => $service1, 'service2' => $service2]);

        $pass = new AddExpressionLanguageProvidersPass();
        $pass->process($container);
    }
}
