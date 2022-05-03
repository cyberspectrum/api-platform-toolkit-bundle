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

use Symfony\Component\Serializer\Annotation as Serializer;

/**
 * This trait defines automatically all read and write constants if any are defined.
 *
 * @see \CyberSpectrum\ApiPlatformToolkit\Serializer\OperationGroup\SerializerOperationGroups
 *
 * @psalm-require-implements \CyberSpectrum\ApiPlatformToolkit\Serializer\OperationGroup\SerializerOperationGroups
 */
trait SerializerOperationGroupsTrait
{
    /**
     * @return list<string>
     * @Serializer\Ignore
     */
    #[Serializer\Ignore]
    public static function getNormalizeCollectionGroups(): array
    {
        $groups = [];

        defined('self::READ') && $groups[] = self::READ;
        defined('self::READ_COLLECTION') && $groups[] = self::READ_COLLECTION;

        return $groups;
    }

    /**
     * @return list<string>
     * @Serializer\Ignore
     */
    #[Serializer\Ignore]
    public static function getNormalizeItemGroups(): array
    {
        $groups = [];

        defined('self::READ') && $groups[] = self::READ;
        defined('self::READ_ITEM') && $groups[] = self::READ_ITEM;

        return $groups;
    }

    /**
     * @return list<string>
     * @Serializer\Ignore
     */
    #[Serializer\Ignore]
    public static function getDenormalizeCreateGroups(): array
    {
        $groups = [];

        defined('self::WRITE') && $groups[] = self::WRITE;
        defined('self::WRITE_CREATE') && $groups[] = self::WRITE_CREATE;

        return $groups;
    }

    /**
     * @return list<string>
     * @Serializer\Ignore
     */
    #[Serializer\Ignore]
    public static function getDenormalizeUpdateGroups(): array
    {
        $groups = [];

        defined('self::WRITE') && $groups[] = self::WRITE;
        defined('self::WRITE_UPDATE') && $groups[] = self::WRITE_UPDATE;

        return $groups;
    }
}
