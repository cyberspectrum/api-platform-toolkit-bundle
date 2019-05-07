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

namespace CyberSpectrum\ApiPlatformToolkit\Serializer\OperationGroup;

use ApiPlatform\Core\Metadata\Resource\Factory\ResourceMetadataFactoryInterface;
use ApiPlatform\Core\Metadata\Resource\ResourceMetadata;

/**
 * This class fixups the resources.
 */
class ResourceMetadataFactory implements ResourceMetadataFactoryInterface
{
    /**
     * @var ResourceMetadataFactoryInterface
     */
    private $decorated;

    /**
     * Create a new instance.
     *
     * @param ResourceMetadataFactoryInterface $decorated The decorated factory.
     */
    public function __construct(ResourceMetadataFactoryInterface $decorated)
    {
        $this->decorated = $decorated;
    }

    /**
     * {@inheritDoc}
     */
    public function create(string $resourceClass): ResourceMetadata
    {
        $resourceMetadata = $this->decorated->create($resourceClass);
        if (!array_key_exists(SerializerOperationGroups::class, class_implements($resourceClass))) {
            return $resourceMetadata;
        }
        /** @var SerializerOperationGroups $resourceClass */
        $collectionOperations = $resourceMetadata->getCollectionOperations();
        foreach ($collectionOperations as $name => &$collectionOperation) {
            switch ($name) {
                case 'post':
                    $this->denormalizationContext($collectionOperation, $resourceClass::getDenormalizeCreateGroups());
                    // POST returns a single item - so we use item normalization here.
                    $this->normalizationContext($collectionOperation, $resourceClass::getNormalizeItemGroups());
                    break;
                case 'get':
                    $this->normalizationContext($collectionOperation, $resourceClass::getNormalizeCollectionGroups());
                    break;
            }
        }
        $resourceMetadata = $resourceMetadata->withCollectionOperations($collectionOperations);
        unset($collectionOperations, $collectionOperation);

        $itemOperations = $resourceMetadata->getItemOperations();
        foreach ($itemOperations as $name => &$itemOperation) {
            if (!isset($itemOperation['groups'])) {
                $itemOperation['groups'] = [];
            }
            switch ($name) {
                case 'put':
                    $this->denormalizationContext($itemOperation, $resourceClass::getDenormalizeUpdateGroups());
                    // No break here.
                case 'get':
                    $this->normalizationContext($itemOperation, $resourceClass::getNormalizeItemGroups());
            }
        }
        $resourceMetadata = $resourceMetadata->withItemOperations($itemOperations);
        unset($itemOperations, $itemOperation);

        $attributes = $resourceMetadata->getAttributes();

        // Remove the "empty" group.
        if (isset($attributes['normalization_context']['groups']) && $attributes['normalization_context']['groups'] === ['empty']) {
            $attributes['normalization_context']['groups'] = [];
            unset($attributes['normalization_context']['groups']);
        }
        if (isset($attributes['denormalization_context']['groups']) && $attributes['denormalization_context']['groups'] === ['empty']) {
            $attributes['denormalization_context']['groups'] = [];
            unset($attributes['denormalization_context']['groups']);
        }

        $resourceMetadata = $resourceMetadata->withAttributes($attributes);

        return $resourceMetadata;
    }

    private function normalizationContext(array &$operation, $additionalGroups)
    {
        if (!isset($operation['normalization_context']['groups'])) {
            $operation['normalization_context']['groups'] = [];
        }
        $operation['normalization_context']['groups'] = array_unique(array_merge(
                $operation['normalization_context']['groups'],
                $additionalGroups
        ));
    }

    private function denormalizationContext(array &$operation, $additionalGroups)
    {
        if (!isset($operation['denormalization_context']['groups'])) {
            $operation['denormalization_context']['groups'] = [];
        }
        $operation['denormalization_context']['groups'] = array_unique(array_merge(
                $operation['denormalization_context']['groups'],
                $additionalGroups
        ));
    }
}
