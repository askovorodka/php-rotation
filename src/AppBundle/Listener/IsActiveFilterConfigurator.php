<?php

namespace AppBundle\Listener;

use AppBundle\Helper\MobileRequestHelper;
use Doctrine\Common\Annotations\Reader;
use Doctrine\Common\Persistence\ObjectManager;
use FOS\UserBundle\Model\UserInterface;
use Symfony\Component\HttpFoundation\AcceptHeader;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * Class IsActiveFilterConfigurator
 * @package AppBundle\Listener
 */
final class IsActiveFilterConfigurator
{
    private $em;
    private $tokenStorage;
    private $reader;
    private $checker;
    private $currentRequest;

    public function __construct(
        ObjectManager $manager,
        TokenStorageInterface $storage,
        Reader $reader,
        AuthorizationCheckerInterface $checker,
        RequestStack $requestStack
    ) {
        $this->em = $manager;
        $this->tokenStorage = $storage;
        $this->reader = $reader;
        $this->checker = $checker;
        $this->currentRequest = $requestStack->getCurrentRequest();
    }

    public function onKernelRequest()
    {
        try {

            if (!MobileRequestHelper::isMobileOperationName(MobileRequestHelper::getRouteName($this->currentRequest))) {
                return '';
            }
            if ($this->isAuthUser()) {
                return '';
            }
            $filter = $this->em->getFilters()->enable('is_active_filter');
            $filter->setAnnotationReader($this->reader);

        } catch (\Throwable $exception) {
            return '';
        }
    }

    /**
     * method check id user authenticated
     * minimal granted by ROLE_USER
     * @return bool
     */
    private function isAuthUser()
    {
        try {
            return $this->checker->isGranted('ROLE_USER');
        } catch (\Throwable $exception) {
            return false;
        }
    }
}
