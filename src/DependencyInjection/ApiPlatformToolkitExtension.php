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

namespace CyberSpectrum\ApiPlatformToolkit\DependencyInjection;

use CyberSpectrum\ApiPlatformToolkit\EventListener\AddAudToJwtListener;
use CyberSpectrum\ApiPlatformToolkit\Serializer\Normalizer\AddApiLoginNormalizer;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

/**
 * This loads the configuration.
 */
class ApiPlatformToolkitExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $config = $this->processConfiguration($this->getConfiguration($configs, $container), $configs);

        $container->setParameter('csap_toolkit.enable_expression_language', $config['enable_expression_language']);

        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));

        if ($config['lexik_jwt']['enabled']) {
            $loader->load('lexik_jwt.yml');
            if (!$config['lexik_jwt']['add_documentation']) {
                $container->removeDefinition(AddApiLoginNormalizer::class);
            }
            if (!$config['lexik_jwt']['add_aud']) {
                $container->removeDefinition(AddAudToJwtListener::class);
            }
            $container->setParameter('csap_toolkit.lexik_jwt_default_ttl', $config['lexik_jwt']['default_ttl']);
            $container->setParameter('csap_toolkit.lexik_jwt_login_url', $config['lexik_jwt']['login_url']);
        }

        $loader->load('services.yml');
    }
}
