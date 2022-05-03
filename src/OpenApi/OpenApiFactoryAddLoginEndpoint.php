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

namespace CyberSpectrum\ApiPlatformToolkit\OpenApi;

use ApiPlatform\Core\OpenApi\Factory\OpenApiFactoryInterface;
use ApiPlatform\Core\OpenApi\Model\MediaType;
use ApiPlatform\Core\OpenApi\Model\Operation;
use ApiPlatform\Core\OpenApi\Model\PathItem;
use ApiPlatform\Core\OpenApi\Model\RequestBody;
use ApiPlatform\Core\OpenApi\Model\Response;
use ApiPlatform\Core\OpenApi\OpenApi;
use ArrayObject;

final class OpenApiFactoryAddLoginEndpoint implements OpenApiFactoryInterface
{
    private OpenApiFactoryInterface $decorated;

    /**
     * The allowed formats.
     * @var list<string>
     */
    private array $allowedFormats;

    private string $loginUri;

    private ?string $loginRefreshUri;

    private ?int $defaultTtl;

    /**
     * @param array<string, list<string>> $allowedFormats
     */
    public function __construct(
        OpenApiFactoryInterface $decorated,
        array $allowedFormats,
        string $loginUri,
        ?string $loginRefreshUri,
        ?int $defaultTtl
    ) {
        $this->decorated = $decorated;
        $this->allowedFormats = array_map(function (array $value): string {
            return $value[0];
        }, array_values($allowedFormats));

        $this->loginUri        = $loginUri;
        $this->loginRefreshUri = $loginRefreshUri;
        $this->defaultTtl      = $defaultTtl;
    }

    public function __invoke(array $context = []): OpenApi
    {
        $openApi = $this->decorated->__invoke($context);
        $this->addLoginResource($openApi);

        return $openApi;
    }

    private function addLoginResource(OpenApi $openApi): void
    {
        $path = $this->getPath($this->loginUri, $openApi);
        // Already defined, do not override.
        if (null !== $path->getPost()) {
            $this->addLoginComponents($openApi);
            return;
        }

        $operation = new Operation(
            'csap_toolkit_json_login',
            ['Authentication'],
            [
                200 => new Response(
                    'Returns the Javascript Web token',
                    $this->createMediaTypes('JWToken'),
                ),
                401 => new Response('Bad credentials')
            ],
            'Create a Javascript Web token.',
            'Create a Javascript Web token.',
            null,
            [],
            new RequestBody(
                'The login data.',
                $this->createMediaTypes('LoginData'),
                true
            )
        );

        $openApi->getPaths()->addPath($this->loginUri, $path->withPost($operation));
        $this->addLoginComponents($openApi);
    }

    private function createMediaTypes(string $schemaName): ArrayObject
    {
        $types = [];
        foreach ($this->allowedFormats as $allowedFormat) {
            $types[$allowedFormat] = new MediaType(
                new ArrayObject(['$ref' => '#/components/schemas/' . $this->mediaTypeName($allowedFormat, $schemaName)])
            );
        }
        return new ArrayObject($types);
    }

    private function addLoginComponents(OpenApi $openApi): void
    {
        $schemas = $openApi->getComponents()->getSchemas();
        if (null === $schemas) {
            return;
        }

        $properties = [
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
        ];
        if (null !== $this->defaultTtl) {
            $properties['ttl'] = [
                'type' => 'integer',
                'example' => '3600',
                'description' => 'The duration this token shall be valid.',
                'default' => $this->defaultTtl,
            ];
        }
        $loginData = new ArrayObject([
            'type' => 'object',
            'description' => 'Login data',
            'properties' =>  $properties,
        ]);

        foreach ($this->allowedFormats as $allowedFormat) {
            $schemaName = $this->mediaTypeName($allowedFormat, 'LoginData');
            if (!$schemas->offsetExists($schemaName)) {
                $schemas->offsetSet($schemaName, clone $loginData);
            }
            $schemaName = $this->mediaTypeName($allowedFormat, 'JWToken');
            if (!$schemas->offsetExists($schemaName)) {
                $schemas->offsetSet(
                    $schemaName,
                    new ArrayObject([
                        'type' => 'object',
                        'description' => 'A JavaScript Web Token',
                        'properties' => [
                            'token' => [
                                'type' => 'string',
                                'readOnly' => true,
                                'description' => 'The token string.'
                            ],
                        ],
                    ])
                );
            }
        }
    }

    private function mediaTypeName(string $mediaType, string $schemaName): string
    {
        return $schemaName . implode('', array_map(function (string $part): string {
            $value = ucfirst($part);
            return preg_replace('#[^a-zA-Z\d]#', '', $value);
        }, explode('/', $mediaType)));
    }

    private function getPath(string $uri, OpenApi $openApi): PathItem
    {
        $paths = $openApi->getPaths();

        if (null !== ($path = $paths->getPath($uri))) {
            return $path;
        }

        return new PathItem();
    }
}
