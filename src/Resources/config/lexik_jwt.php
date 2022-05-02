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

use CyberSpectrum\ApiPlatformToolkit\EventListener\AddAudToJwtListener;
use CyberSpectrum\ApiPlatformToolkit\EventListener\OverrideJwtTtlListener;
use CyberSpectrum\ApiPlatformToolkit\Serializer\Normalizer\AddApiLoginNormalizer;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

use function Symfony\Component\DependencyInjection\Loader\Configurator\param;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

return function (ContainerConfigurator $configurator): void {
    $services = $configurator->services();
    $services
        ->set(OverrideJwtTtlListener::class)
        ->arg('$requestStack', service('request_stack'))
        ->arg('$defaultTtl', param('csap_toolkit.lexik_jwt_default_ttl'))
        ->tag(
            'kernel.event_listener',
            ['event' => 'lexik_jwt_authentication.on_jwt_created']
        );
    $services->set(AddAudToJwtListener::class)
        ->arg('$requestStack', service('request_stack'))
        ->tag(
            'kernel.event_listener',
            ['event' => 'lexik_jwt_authentication.on_jwt_created']
        );

    $services->set(AddApiLoginNormalizer::class)
        ->decorate('api_platform.swagger.normalizer.documentation', null, 100)
        ->arg('$decorated', service('.inner'))
        ->arg('$allowedFormats', param('api_platform.formats'))
        ->arg('$parameters', [
            'login_url' => param('csap_toolkit.lexik_jwt_login_url'),
            'default_ttl' => param('csap_toolkit.lexik_jwt_default_ttl'),
        ]);
};
