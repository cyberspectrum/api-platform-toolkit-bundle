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

namespace CyberSpectrum\ApiPlatformToolkit\Tests;

use CyberSpectrum\ApiPlatformToolkit\ApiPlatformToolkitBundle;
use CyberSpectrum\ApiPlatformToolkit\DependencyInjection\Compiler\AddExpressionLanguageProvidersPass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class ApiPlatformToolkitBundleTest extends TestCase
{
    /**
     * Test.
     *
     * @return void
     */
    public function testRegistersCompilerPass()
    {
        $bundle = new ApiPlatformToolkitBundle();

        $container = $this->getMockBuilder(ContainerBuilder::class)->getMock();
        $container->expects($this->once())->method('addCompilerPass')->willReturnCallback(function ($pass) {
            $this->assertInstanceOf(AddExpressionLanguageProvidersPass::class, $pass);
        });

        $bundle->build($container);
    }
}
