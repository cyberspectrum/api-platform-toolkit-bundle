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
 *
 * @psalm-type TCyberSpectrumApiPlatformToolkitConfiguration=array{
 *   lexik_jwt: array{
 *     enabled: bool,
 *     add_documentation: bool,
 *     add_aud: bool,
 *     default_ttl: int,
 *     json_login_url: string,
 *     json_login_refresh_url: ?string,
 *   },
 *   enable_expression_language: bool,
 *   openapi_docs: array{
 *     enabled: bool,
 *     remove_html_format: bool,
 *   },
 * }
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
                ->arrayNode('openapi_docs')
                    ->canBeDisabled()
                    ->children()
                        ->booleanNode('remove_html_format')
                            ->info('If set, this removes the text/html from the allowed formats in swagger UI.')
                            ->defaultValue(true)
                        ->end()
                    ->end()
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
                        ->scalarNode('json_login_url')
                            ->defaultValue('/api/login_check')
                        ->end()
                        ->scalarNode('json_login_refresh_url')
                            ->defaultNull()
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
