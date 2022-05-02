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

namespace CyberSpectrum\ApiPlatformToolkit\Serializer\Normalizer;

use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

use function array_merge_recursive;
use function assert;
use function is_array;

/**
 * Class AddApiLoginNormalizer - Adds the authentication endpoint to the swagger UI.
 *
 * @see https://gist.github.com/alborq/9ec969cdbb1f697d0b11a7a0eb3734bb
 *
 * @psalm-type TAddApiLoginNormalizerParameters = array{
 *   login_url: string,
 *   login_refresh: string
 * }
 */
final class AddApiLoginNormalizer implements NormalizerInterface
{
    /** The parent normalizer. */
    private NormalizerInterface $normalizerDeferred;

    /** The allowed formats. */
    private array $allowedFormats;

    private string $loginUri;

    private string $loginRefreshUri;

    /**
     * Create a new instance.
     *
     * @param NormalizerInterface              $normalizerDeferred the normalizer to use
     * @param array<string, list<string>>      $allowedFormats     the allowed formats from the serializer
     * @param TAddApiLoginNormalizerParameters $parameters         the parameters
     */
    public function __construct(
        NormalizerInterface $normalizerDeferred,
        array $allowedFormats,
        array $parameters
    ) {
        $this->normalizerDeferred = $normalizerDeferred;
        $this->allowedFormats = array_map(function (array $value): string {
            return $value[0];
        }, array_values($allowedFormats));

        $this->loginUri        = $parameters['login_url'];
        $this->loginRefreshUri = $parameters['login_refresh'];
    }

    /**
     * {@inheritdoc}
     */
    public function normalize($object, string $format = null, array $context = [])
    {
        $previous = $this->normalizerDeferred->normalize($object, $format, $context);
        assert(is_array($previous));

        return array_merge_recursive($this->getTokenLoginDocumentation(), $previous);
    }

    /**
     * {@inheritdoc}
     */
    public function supportsNormalization($data, string $format = null): bool
    {
        return $this->normalizerDeferred->supportsNormalization($data, $format);
    }

    /**
     * Generate the documentation for the login endpoint.
     */
    private function getTokenLoginDocumentation(): array
    {
        return [
            'paths' => [
                $this->loginUri => [
                    'post' => [
                        'tags'        => ['Authenticate'],
                        'operationId' => 'login',
                        'consumes'    => $this->allowedFormats,
                        'produces'    => $this->allowedFormats,
                        'summary'     => 'Get JW token to login.',
                        'parameters'  => [
                            [
                                'in'       => 'body',
                                'name'     => 'user',
                                'required' => true,
                                'schema'   => [
                                    'type'        => 'object',
                                    'description' =>
                                        'Username and password of the user with optional life time for the token.',
                                    'properties'  => [
                                        'username' => [
                                            'type'        => 'string',
                                            'example'     => 'j.doe',
                                            'description' => 'The username.',
                                        ],
                                        'password' => [
                                            'type'        => 'string',
                                            'example'     => 's3cret',
                                            'description' => 'The password.',
                                        ],
                                    ],
                                ],
                            ],
                        ],
                        'responses' => [
                            200 => [
                                'description' => 'Returns the JW token',
                                'schema'      => [
                                    '$ref' => '#/definitions/JWToken',
                                ],
                            ],
                            401 => [
                                'description' => 'Bad credentials',
                            ],
                        ],
                    ],
                ],
                $this->loginRefreshUri => [
                    'get' => [
                        'tags'        => ['Authenticate'],
                        'operationId' => 'login_refresh',
                        'produces'    => $this->allowedFormats,
                        'summary'     => 'Refresh JW token.',
                        'parameters'  => [],
                        'responses'   => [
                            200 => [
                                'description' => 'Returns the refreshed JW token',
                                'schema'      => [
                                    '$ref' => '#/definitions/JWToken',
                                ],
                            ],
                            401 => [
                                'description' => 'Bad credentials when the token was invalid',
                            ],
                        ],
                    ],
                ],
            ],
            'definitions' => [
                'JWToken' => [
                    'type'        => 'object',
                    'description' => 'A JavaScript Web Token',
                    'properties'  => [
                        'token' => [
                            'type'     => 'string',
                            'readOnly' => true,
                        ],
                    ],
                    'required' => ['token'],
                ],
            ],
        ];
    }
}
