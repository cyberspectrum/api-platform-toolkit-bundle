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

namespace CyberSpectrum\ApiPlatformToolkit\Serializer\OperationGroup;

use ApiPlatform\Core\Metadata\Resource\Factory\ResourceMetadataFactoryInterface;
use ApiPlatform\Core\Metadata\Resource\ResourceMetadata;

/**
 * This class fixes the resources.
 *
 * @psalm-type TResourceOperation = array{
 *     normalization_context?: array{groups?: list<string>},
 *     denormalization_context?: array{groups?: list<string>}
 *  }
 * @psalm-type TResourceOperationContextName='normalization_context'|'denormalization_context'
 */
final class ResourceMetadataFactory implements ResourceMetadataFactoryInterface
{
    private ResourceMetadataFactoryInterface $decorated;

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

        $resourceMetadata = $this->updateCollectionOperations($resourceMetadata, $resourceClass);
        $resourceMetadata = $this->updateItemOperations($resourceMetadata, $resourceClass);

        return $this->cleanAttributes($resourceMetadata);
    }

    private function updateCollectionOperations(
        ResourceMetadata $resourceMetadata,
        string $resourceClass
    ): ResourceMetadata {
        /** @var array<string, TResourceOperation>|null $collectionOperations */
        $collectionOperations = $resourceMetadata->getCollectionOperations();
        if (null == $collectionOperations) {
            return $resourceMetadata;
        }

        /** @var SerializerOperationGroups $resourceClass - pretty hacky but how else to denote static invocation? */
        foreach ($collectionOperations as $name => &$collectionOperation) {
            switch ($name) {
                case 'post':
                    $this->updateContext(
                        $collectionOperation,
                        'denormalization_context',
                        $resourceClass::getDenormalizeCreateGroups()
                    );
                    // POST returns a single item - so we use item normalization here.
                    $this->updateContext(
                        $collectionOperation,
                        'normalization_context',
                        $resourceClass::getNormalizeItemGroups()
                    );
                    break;
                case 'get':
                    $this->updateContext(
                        $collectionOperation,
                        'normalization_context',
                        $resourceClass::getNormalizeCollectionGroups()
                    );
                    break;
            }
        }
        return $resourceMetadata->withCollectionOperations($collectionOperations);
    }

    private function updateItemOperations(ResourceMetadata $resourceMetadata, string $resourceClass): ResourceMetadata
    {
        /** @var array<string, TResourceOperation>|null $itemOperations */
        $itemOperations = $resourceMetadata->getItemOperations();
        if (null == $itemOperations) {
            return $resourceMetadata;
        }

        /** @var SerializerOperationGroups $resourceClass - pretty hacky but how else to denote static invocation? */
        foreach ($itemOperations as $name => &$itemOperation) {
            if (!isset($itemOperation['groups'])) {
                $itemOperation['groups'] = [];
            }
            switch ($name) {
                case 'put':
                    $this->updateContext(
                        $itemOperation,
                        'denormalization_context',
                        $resourceClass::getDenormalizeUpdateGroups()
                    );
                // No break here.
                case 'get':
                    $this->updateContext(
                        $itemOperation,
                        'normalization_context',
                        $resourceClass::getNormalizeItemGroups()
                    );
            }
        }
        return $resourceMetadata->withItemOperations($itemOperations);
    }

    /**
     * @param TResourceOperation $operation
     * @param TResourceOperationContextName $contextName
     * @param list<string> $additionalGroups
     *
     * @psalm-suppress ReferenceConstraintViolation - revisit when @param-out works correctly.
     */
    private function updateContext(array &$operation, string $contextName, array $additionalGroups): void
    {
        $operation[$contextName]['groups'] = array_unique(array_merge(
            $operation[$contextName]['groups'] ?? [],
            $additionalGroups
        ));
    }

    private function cleanAttributes(ResourceMetadata $resourceMetadata): ResourceMetadata
    {
        /** @var array{
         *     normalization_context?: array{groups: list<string>},
         *     denormalization_context?: array{groups: list<string>}
         *  } $attributes */
        $attributes = $resourceMetadata->getAttributes();

        // Remove the "empty" group.
        if (
            isset($attributes['normalization_context']['groups'])
            && $attributes['normalization_context']['groups'] === ['empty']
        ) {
            unset($attributes['normalization_context']['groups']);
        }
        if (
            isset($attributes['denormalization_context']['groups'])
            && $attributes['denormalization_context']['groups'] === ['empty']
        ) {
            unset($attributes['denormalization_context']['groups']);
        }

        return $resourceMetadata->withAttributes($attributes);
    }
}
