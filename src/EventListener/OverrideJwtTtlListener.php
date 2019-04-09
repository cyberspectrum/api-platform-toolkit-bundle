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
 * This allows to override the TTL in a JWT within the request.
 */
class OverrideJwtTtlListener
{
    /**
     * The request stack.
     *
     * @var RequestStack
     */
    private $requestStack;

    /**
     * The default ttl to use.
     *
     * @var int
     */
    private $defaultTtl;

    /**
     * Create a new instance.
     *
     * @param RequestStack $requestStack The request stack.
     * @param int          $defaultTtl   The default TTL to use.
     */
    public function __construct(RequestStack $requestStack, int $defaultTtl)
    {
        $this->requestStack = $requestStack;
        $this->defaultTtl   = $defaultTtl;
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
        $ttl     = $this->defaultTtl;
        if (($data = json_decode($request->getContent(), true)) && array_key_exists('ttl', $data)) {
            $ttl = (int) $data['ttl'];
        }
        switch ($ttl) {
            case -1:
                unset($payload['exp']);
                break;
            default:
                $payload['exp'] = (time() + $ttl);
        }

        $event->setData($payload);
    }
}
