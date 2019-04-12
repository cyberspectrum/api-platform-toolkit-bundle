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

use ApiPlatform\Core\Serializer\SerializerContextBuilderInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Context builder for applying the defined serialization groups.
 *
 * @see https://www.thinkbean.com/drupal-development-blog/restrict-properties-api-platform-serialization-groups
 */
class SerializerOperationGroupsContextBuilder implements SerializerContextBuilderInterface
{
    /**
     * The decorated service.
     *
     * @var SerializerContextBuilderInterface
     */
    private $decorated;

    /**
     * Create a new instance.
     *
     * @param SerializerContextBuilderInterface $decorated The decorated service.
     */
    public function __construct(SerializerContextBuilderInterface $decorated)
    {
        $this->decorated = $decorated;
    }

    /**
     * {@inheritDoc}
     */
    public function createFromRequest(Request $request, bool $normalization, array $extractedAttributes = null): array
    {
        $context = $this->decorated->createFromRequest($request, $normalization, $extractedAttributes);
        if ($context['input'] || $context['output']) {
            return $context;
        }
        $subject = $context['resource_class'];
        if (!array_key_exists(SerializerOperationGroups::class, class_implements($subject))) {
            return $context;
        }
        /** @var SerializerOperationGroups $subject */

        if ($normalization) {
            if (isset($extractedAttributes['item_operation_name'])) {
                $groups = $subject::getNormalizeItemGroups();
            } else {
                $groups = $subject::getNormalizeCollectionGroups();
            }
        } else {
            if (Request::METHOD_PUT === $request->getMethod()) {
                $groups = $subject::getDenormalizeUpdateGroups();
            } else {
                $groups = $subject::getDenormalizeCreateGroups();
            }
        }

        if (!isset($context['groups'])) {
            $context['groups'] = [];
        }
        $context['groups'] = array_unique(array_merge($context['groups'], $groups));

        return $context;
    }
}
