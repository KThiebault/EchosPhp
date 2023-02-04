<?php

declare(strict_types=1);

namespace App\Controller\Security;

use App\Controller\BaseController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

#[Route('/login', name: 'app_security_login', methods: [Request::METHOD_GET, Request::METHOD_POST])]
final class LoginSecurityController extends BaseController
{
    public function __invoke(AuthenticationUtils $authenticationUtils): Response
    {
        return $this->render('security/login.html.twig', [
            'last_username' => $authenticationUtils->getLastUsername(),
            'error' => $authenticationUtils->getLastAuthenticationError(),
        ]);
    }
}
