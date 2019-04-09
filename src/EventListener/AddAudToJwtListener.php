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

namespace CyberSpectrum\ApiPlatformToolkit\EventListener;

use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * This adds the 'aud' key to the JWT.
 */
class AddAudToJwtListener
{
    /**
     * The request stack.
     *
     * @var RequestStack
     */
    private $requestStack;

    /**
     * Create a new instance.
     *
     * @param RequestStack $requestStack The request stack.
     */
    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    /**
     * Override or unset the ttl parameter.
     *
     * @param JWTCreatedEvent $event The event to process.
     *
     * @return void
     */
    public function onJWTCreated(JWTCreatedEvent $event): void
    {
        $payload = $event->getData();
        $request = $this->requestStack->getCurrentRequest();

        $payload['aud'] = $request->getSchemeAndHttpHost();

        $event->setData($payload);
    }
}
