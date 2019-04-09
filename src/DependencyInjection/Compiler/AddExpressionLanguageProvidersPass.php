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

namespace CyberSpectrum\ApiPlatformToolkit\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Registers the expression language providers to the api-platform expression language.
 */
class AddExpressionLanguageProvidersPass implements CompilerPassInterface
{
    /**
     * Service tag to collect expression language providers.
     */
    const SERVICE_TAG = 'csap_toolkit.security.expression_language_provider';

    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->getParameter('csap_toolkit.enable_expression_language')) {
            return;
        }

        if ($container->has('api_platform.security.expression_language')) {
            $definition = $container->findDefinition('api_platform.security.expression_language');
            foreach (array_keys($container->findTaggedServiceIds(self::SERVICE_TAG, true)) as $id) {
                // Ensure we do not have the call present already (if api-platform should decide to implement the tag.
                $definition->addMethodCall('registerProvider', [new Reference($id)]);
            }
        }
    }
}
