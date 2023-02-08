<?php

declare(strict_types=1);

namespace App\Controller\Security;

use App\Controller\BaseController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/logout', name: 'app_security_logout', methods: Request::METHOD_GET)]
final class LogoutSecurityController extends BaseController
{
    public function __invoke(): Response
    {
        throw new \Exception('This should never be reached!');
    }
}
