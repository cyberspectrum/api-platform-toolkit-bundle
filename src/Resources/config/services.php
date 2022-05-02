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

use CyberSpectrum\ApiPlatformToolkit\OpenApi\OpenApiFactoryRemoveHtmlFormat;
use CyberSpectrum\ApiPlatformToolkit\Serializer\OperationGroup\ResourceMetadataFactory;
use CyberSpectrum\ApiPlatformToolkit\Serializer\OperationGroup\SerializerOperationGroupsContextBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

return function (ContainerConfigurator $configurator): void {
    $services = $configurator->services();
    $services
        ->set(SerializerOperationGroupsContextBuilder::class)
        ->decorate('api_platform.serializer.context_builder')
        ->arg('$decorated', service('.inner'))
        ->autoconfigure(false);
    $services
        ->set(ResourceMetadataFactory::class)
        ->decorate('api_platform.metadata.resource.metadata_factory')
        ->arg('$decorated', service('.inner'))
        ->autoconfigure(false);

    $services->set(OpenApiFactoryRemoveHtmlFormat::class)
        ->decorate('api_platform.openapi.factory')
        ->arg('$decorated', service('.inner'));
};
