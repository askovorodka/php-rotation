<?php

namespace AppBundle\Listener;

use AppBundle\Helper\MobileRequestHelper;
use Doctrine\Common\Annotations\Reader;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\AcceptHeader;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

final class IsProductionFilterConfigurator
{
    private $em;
    private $reader;
    private $checker;
    private $currentRequest;

    public function __construct(
        ObjectManager $manager,
        Reader $reader,
        AuthorizationCheckerInterface $checker,
        RequestStack $requestStack
    ) {
        $this->em = $manager;
        $this->reader = $reader;
        $this->checker = $checker;
        $this->currentRequest = $requestStack->getCurrentRequest();
    }

    public function onKernelRequest()
    {
        try {
            /**
             * if not mobile request
             */
            if (!MobileRequestHelper::isMobileOperationName(MobileRequestHelper::getRouteName($this->currentRequest))) {
                return '';
            }
            if ($this->isAuthUser()) {
                return '';
            }
            $filter = $this->em->getFilters()->enable('is_production_filter');
            $filter->setAnnotationReader($this->reader);
        } catch (\Throwable $exception) {
            return '';
        }
    }

    private function isAuthUser()
    {
        try {
            return $this->checker->isGranted('ROLE_USER');
        } catch (\Throwable $exception) {
            return false;
        }
    }
}
