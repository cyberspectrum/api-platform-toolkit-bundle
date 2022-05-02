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

/** Interface to help automatically defining normalization and denormalization groups on a per entity basis. */
interface SerializerOperationGroups
{
    public const ALL_PROPS_HIDDEN = 'empty';

    /**
     * This returns the normalization groups to use when a collection operation is issued.
     *
     * @return list<string>
     */
    public static function getNormalizeCollectionGroups(): array;

    /**
     * This returns normalization groups to use when an item operation is issued.
     *
     * @return list<string>
     */
    public static function getNormalizeItemGroups(): array;

    /**
     * This returns normalization groups to use when an item create operation is issued.
     *
     * @return list<string>
     */
    public static function getDenormalizeCreateGroups(): array;

    /**
     * This returns normalization groups to use when an item update operation is issued.
     *
     * @return list<string>
     */
    public static function getDenormalizeUpdateGroups(): array;
}
