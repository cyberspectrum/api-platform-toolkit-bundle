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

namespace CyberSpectrum\ApiPlatformToolkit\Serializer\Normalizer;

use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * Class AddApiLoginNormalizer - Adds the authentication endpoint to the swagger UI.
 *
 * @see https://gist.github.com/alborq/9ec969cdbb1f697d0b11a7a0eb3734bb
 */
final class AddApiLoginNormalizer implements NormalizerInterface
{
    /**
     * The parent normalizer.
     *
     * @var NormalizerInterface
     */
    private $normalizerDeferred;

    /**
     * The allowed formats.
     *
     * @var array
     */
    private $allowedFormats;

    /**
     * The parameters.
     *
     * @var array
     */
    private $parameters;

    /**
     * Create a new instance.
     *
     * @param NormalizerInterface $normalizerDeferred The normalizer to use.
     * @param array               $allowedFormats     The allowed formats from the serializer.
     * @param array               $parameters         The parameters.
     */
    public function __construct(
        NormalizerInterface $normalizerDeferred,
        array $allowedFormats,
        array $parameters
    ) {
        $this->normalizerDeferred = $normalizerDeferred;
        $this->allowedFormats     = array_map(function ($value) {
            return $value[0];
        }, array_values($allowedFormats));
        $this->parameters = $parameters;
    }

    /**
     * {@inheritdoc}
     */
    public function normalize($object, $format = null, array $context = [])
    {
        return array_merge_recursive(
            $this->getTokenLoginDocumentation(),
            $this->normalizerDeferred->normalize($object, $format, $context)
        );
    }

    /**
     * {@inheritdoc}
     */
    public function supportsNormalization($data, $format = null)
    {
        return $this->normalizerDeferred->supportsNormalization($data, $format);
    }

    /**
     * Generate the documentation for the login endpoint.
     *
     * @return array
     */
    private function getTokenLoginDocumentation(): array
    {
        return [
            'paths' => [
                $this->parameters['login_url'] => [
                    'post' => [
                        'tags' => ['JWToken'],
                        'operationId' => 'login',
                        'consumes' => $this->allowedFormats,
                        'produces' => $this->allowedFormats,
                        'summary' => 'Get JW token to login.',
                        'parameters' => [
                            [
                                'in' => 'body',
                                'name' => 'user',
                                'required' => true,
                                'schema' => [
                                    'type' => 'object',
                                    'description' => 'Username and password of the user with optional life time for the token.',
                                    'properties' => [
                                        'username' => [
                                            'type' => 'string',
                                            'example' => 'j.doe',
                                            'description' => 'The username.'
                                        ],
                                        'password' => [
                                            'type' => 'string',
                                            'example' => 's3cret',
                                            'description' => 'The password.'
                                        ],
                                        'ttl' => [
                                            'type' => 'integer',
                                            'example' => '3600',
                                            'description' => 'The duration this token shall be valid.',
                                            'default' => $this->parameters['default_ttl']
                                        ]
                                    ]
                                ]
                            ]
                        ],
                        'responses' => [
                            200 => [
                                'description' => 'Returns the JW token',
                                'schema' => [
                                    '$ref' => '#/definitions/JWToken'
                                ]
                            ],
                            401 => [
                                'description' => 'Bad credentials'
                            ]
                        ],
                    ]
                ]
            ],
            'definitions' => [
                'JWToken' => [
                    'type' => 'object',
                    'description' => 'A JavaScript Web Token',
                    'properties' => [
                        'token' => [
                            'type' => 'string',
                            'readOnly' => true,
                        ]
                    ],
                    'required' => ['token'],
                ]
            ]
        ];
    }
}
