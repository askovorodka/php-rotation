<?php

namespace AppBundle\Listener;

use ApiPlatform\Core\Util\RequestAttributesExtractor;
use AppBundle\Entity\Hotel;
use AppBundle\Exception\ResponseMobileListenerException;
use AppBundle\Helper\MobileRequestHelper;
use Symfony\Component\HttpFoundation\AcceptHeader;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;

class ResponseMobileListener
{
    public function onKernelResponse(FilterResponseEvent $event)
    {
        try {
            $content = $event->getResponse()->getContent();
            if (!MobileRequestHelper::isMobileOperationName(MobileRequestHelper::getRouteName($event->getRequest()))) {
                return;
            }
            $response = $event->getResponse();
            $response->headers->set('vary', []);
            //add cache-control: public, max-age=3600
            $response->headers->addCacheControlDirective('public', true);
            $response->headers->addCacheControlDirective('max-age=3600', true);
        } catch (\Throwable $exception) {
        } finally {
            $event->getResponse()->setContent($content);
        }
    }
}
