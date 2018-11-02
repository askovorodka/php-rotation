<?php

namespace AppBundle\Action;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;

/**
 * Action для пинга авторизации пользователя
 *
 * @package AppBundle\Action
 */
class AuthCheckAction extends AbstractPhotoUploadAction
{
    /**
     * @Route(
     *     name="auth_check",
     *     path="/auth-check",
     * )
     *
     * @Method({"GET"})
     */
    public function __invoke()
    {
        return new Response(json_encode('Hello!'), 200);
    }
}
