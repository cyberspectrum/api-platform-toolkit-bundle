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

use CyberSpectrum\ApiPlatformToolkit\EventListener\AddAudToJwtListener;
use CyberSpectrum\ApiPlatformToolkit\OpenApi\OpenApiFactoryAddLoginEndpoint;
use CyberSpectrum\ApiPlatformToolkit\OpenApi\OpenApiFactoryRemoveHtmlFormat;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;

/**
 * This loads the configuration.
 *
 * @psalm-import-type TCyberSpectrumApiPlatformToolkitConfiguration from Configuration
 */
class ApiPlatformToolkitExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $configuration = $this->getConfiguration($configs, $container);
        assert($configuration instanceof Configuration);
        /** @var TCyberSpectrumApiPlatformToolkitConfiguration $config */
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new PhpFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));

        if ($config['lexik_jwt']['enabled']) {
            $loader->load('lexik_jwt.php');
            if (!$config['lexik_jwt']['add_documentation']) {
                $container->removeDefinition(OpenApiFactoryAddLoginEndpoint::class);
            }
            if (!$config['lexik_jwt']['add_aud']) {
                $container->removeDefinition(AddAudToJwtListener::class);
            }
            $container->setParameter('csap_toolkit.lexik_jwt_default_ttl', $config['lexik_jwt']['default_ttl']);
            $container->setParameter('csap_toolkit.lexik_jwt_login_url', $config['lexik_jwt']['json_login_url']);
            $container->setParameter(
                'csap_toolkit.lexik_jwt_login_refresh_url',
                $config['lexik_jwt']['json_login_refresh_url']
            );
        }

        $loader->load('services.php');

        if ($config['openapi_docs']['enabled']) {
            if (!$config['openapi_docs']['remove_html_format']) {
                $container->removeDefinition(OpenApiFactoryRemoveHtmlFormat::class);
            }
        }
        $container->setParameter(
            'csap_toolkit.enable_expression_language',
            $config['enable_expression_language']
        );
    }
}
