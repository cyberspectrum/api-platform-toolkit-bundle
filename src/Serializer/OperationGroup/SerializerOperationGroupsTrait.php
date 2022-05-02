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

/**
 * This trait defines automatically all read and write constants if any are defined.
 *
 * @see \CyberSpectrum\ApiPlatformToolkit\Serializer\OperationGroup\SerializerOperationGroups
 */
trait SerializerOperationGroupsTrait
{
    /**
     * This returns the normalization groups to use when a collection operation is issued.
     *
     * @return array
     */
    public static function getNormalizeCollectionGroups(): array
    {
        $groups = [];

        defined('self::READ') && $groups[] = self::READ;
        defined('self::READ_COLLECTION') && $groups[] = self::READ_COLLECTION;

        return $groups;
    }

    /**
     * This returns normalization groups to use when an item operation is issued.
     *
     * @return array
     */
    public static function getNormalizeItemGroups(): array
    {
        $groups = [];

        defined('self::READ') && $groups[] = self::READ;
        defined('self::READ_ITEM') && $groups[] = self::READ_ITEM;

        return $groups;
    }

    /**
     * This returns normalization groups to use when an item create operation is issued.
     *
     * @return array
     */
    public static function getDenormalizeCreateGroups(): array
    {
        $groups = [];

        defined('self::WRITE') && $groups[] = self::WRITE;
        defined('self::WRITE_CREATE') && $groups[] = self::WRITE_CREATE;

        return $groups;
    }

    /**
     * This returns normalization groups to use when an item update operation is issued.
     *
     * @return array
     */
    public static function getDenormalizeUpdateGroups(): array
    {
        $groups = [];

        defined('self::WRITE') && $groups[] = self::WRITE;
        defined('self::WRITE_UPDATE') && $groups[] = self::WRITE_UPDATE;

        return $groups;
    }
}
