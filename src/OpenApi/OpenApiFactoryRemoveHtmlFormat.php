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
use ApiPlatform\Core\OpenApi\Model\Operation;
use ApiPlatform\Core\OpenApi\Model\PathItem;
use ApiPlatform\Core\OpenApi\Model\Response;
use ApiPlatform\Core\OpenApi\OpenApi;

final class OpenApiFactoryRemoveHtmlFormat implements OpenApiFactoryInterface
{
    private OpenApiFactoryInterface $decorated;

    public function __construct(OpenApiFactoryInterface $decorated)
    {
        $this->decorated = $decorated;
    }

    public function __invoke(array $context = []): OpenApi
    {
        $openApi = $this->decorated->__invoke($context);
        return $this->walkAllOperations(
            function (Operation $operation): Operation {
                // 1. Clean responses
                /** @var Response $response */
                foreach ($operation->getResponses() as $response) {
                    if ($content = $response->getContent()) {
                        // Clean "text/html" here, we only want to access swagger UI but not have it as request format.
                        if (!$content->offsetExists('text/html')) {
                            continue;
                        }
                        $content->offsetUnset('text/html');
                    }
                }
                unset($response, $content);
                // 2. Clean request body.
                if (null !== ($requestBody = $operation->getRequestBody())) {
                    $content = $requestBody->getContent();
                    // Clean "text/html" here, we only want to access swagger UI but not have it as request format.
                    if (!$content->offsetExists('text/html')) {
                        return $operation;
                    }
                    $content->offsetUnset('text/html');
                }
                return $operation;
            },
            $openApi
        );
    }

    /** @param callable(Operation $operation, PathItem $path): Operation $callback */
    private function walkAllOperations(callable $callback, OpenApi $openApi): OpenApi
    {
        $paths = $openApi->getPaths();
        /** @var array<string, PathItem> $pathArray */
        $pathArray = $paths->getPaths();
        foreach ($pathArray as $uri => $pathItem) {
            $changed = false;
            foreach (
                [
                    'Get',
                    'Put',
                    'Post',
                    'Delete',
                    'Options',
                    'Head',
                    'Patch',
                    'Trace',
                ] as $operationName
            ) {
                /**
                 * @psalm-suppress MixedAssignment
                 * @psalm-suppress MixedMethodCall
                 */
                if (null === $operation = call_user_func([$pathItem, 'get' . $operationName])) {
                    continue;
                }
                assert($operation instanceof Operation);
                $newOperation = call_user_func($callback, $operation, $pathItem);
                if ($newOperation !== $operation) {
                    $changed = true;
                    /** @psalm-suppress MixedAssignment */
                    $pathItem = call_user_func([$pathItem, 'with' . $operationName], $newOperation);
                    assert($pathItem instanceof PathItem);
                }
            }
            if ($changed) {
                $paths->addPath($uri, $pathItem);
            }
        }

        return $openApi;
    }
}
