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

namespace CyberSpectrum\ApiPlatformToolkit\EventListener;

use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;
use Symfony\Component\HttpFoundation\RequestStack;

use function array_key_exists;
use function is_array;
use function json_decode;
use function time;

/**
 * This allows to override the TTL in a JWT within the request.
 */
class OverrideJwtTtlListener
{
    /** The request stack. */
    private RequestStack $requestStack;

    /** The default ttl to use. */
    private int $defaultTtl;

    public function __construct(RequestStack $requestStack, int $defaultTtl)
    {
        $this->requestStack = $requestStack;
        $this->defaultTtl   = $defaultTtl;
    }

    /**
     * Override or unset the ttl parameter.
     *
     * @param JWTCreatedEvent $event The event to process.
     */
    public function __invoke(JWTCreatedEvent $event): void
    {
        $request = $this->requestStack->getCurrentRequest();
        if (null === $request) {
            return;
        }
        $payload = $event->getData();
        $ttl     = $this->defaultTtl;
        /** @var mixed $data */
        $data    = json_decode((string) $request->getContent(), true);
        if (is_array($data) && array_key_exists('ttl', $data)) {
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
