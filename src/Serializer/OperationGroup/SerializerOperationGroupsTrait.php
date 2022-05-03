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
        return self::readConstants('self::READ', 'self::READ_COLLECTION');
    }

    /**
     * @return list<string>
     * @Serializer\Ignore
     */
    #[Serializer\Ignore]
    public static function getNormalizeItemGroups(): array
    {
        return self::readConstants('self::READ', 'self::READ_ITEM');
    }

    /**
     * @return list<string>
     * @Serializer\Ignore
     */
    #[Serializer\Ignore]
    public static function getDenormalizeCreateGroups(): array
    {
        return self::readConstants('self::WRITE', 'self::WRITE_CREATE');
    }

    /**
     * @return list<string>
     * @Serializer\Ignore
     */
    #[Serializer\Ignore]
    public static function getDenormalizeUpdateGroups(): array
    {
        return self::readConstants('self::WRITE', 'self::WRITE_UPDATE');
    }

    /** @return list<string> */
    private static function readConstants(string ...$names): array
    {
        $result = [];
        foreach ($names as $name) {
            if (null !== $value = self::readConstant($name)) {
                $result[] = $value;
            }
        }

        return $result;
    }

    private static function readConstant(string $name): ?string
    {
        /** @var mixed $value */
        $value = constant($name);
        if (is_string($value)) {
            return $value;
        }

        return null;
    }
}
