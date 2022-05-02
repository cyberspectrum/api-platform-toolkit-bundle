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

namespace CyberSpectrum\ApiPlatformToolkit\Tests;

use CyberSpectrum\ApiPlatformToolkit\ApiPlatformToolkitBundle;
use CyberSpectrum\ApiPlatformToolkit\DependencyInjection\Compiler\AddExpressionLanguageProvidersPass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/** @covers \CyberSpectrum\ApiPlatformToolkit\ApiPlatformToolkitBundle */
class ApiPlatformToolkitBundleTest extends TestCase
{
    public function testCanBeInstantiated(): void
    {
        $container = new ContainerBuilder();

        $bundle = new ApiPlatformToolkitBundle();
        $bundle->build($container);

        if ($this->isCompilerPassRegistered($container)) {
            $this->addToAssertionCount(1);
            return;
        }

        self::fail(AddExpressionLanguageProvidersPass::class . ' has not been registered as compiler pass');
    }

    private function isCompilerPassRegistered(ContainerBuilder $container): bool
    {
        $passes = $container->getCompilerPassConfig()->getPasses();
        foreach ($passes as $pass) {
            if ($pass instanceof AddExpressionLanguageProvidersPass) {
                $this->addToAssertionCount(1);
                return true;
            }
        }

        return false;
    }
}
