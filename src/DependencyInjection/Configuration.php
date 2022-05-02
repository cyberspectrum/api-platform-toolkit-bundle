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

namespace CyberSpectrum\ApiPlatformToolkit\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Adds the Contao configuration structure.
 */
class Configuration implements ConfigurationInterface
{
    /**
     * Generates the configuration tree builder.
     *
     * @psalm-suppress MixedMethodCall
     * @psalm-suppress PossiblyUndefinedMethod
     */
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('api_platform_toolkit');
        $rootNode    = $treeBuilder->getRootNode();

        $rootNode
            ->children()
                ->booleanNode('enable_expression_language')
                    ->defaultValue(true)
                ->end()
               ->arrayNode('lexik_jwt')
                    ->canBeDisabled()
                    ->children()
                        ->booleanNode('add_documentation')
                            ->defaultValue(true)
                        ->end()
                        ->integerNode('default_ttl')
                            ->defaultValue(3600)
                        ->end()
                        ->scalarNode('login_url')
                            ->defaultValue('/api/login_check')
                        ->end()
                        ->booleanNode('add_aud')
                            ->defaultValue(true)
                        ->end()
                    ->end()
               ->end()
            ->end();

        return $treeBuilder;
    }
}
