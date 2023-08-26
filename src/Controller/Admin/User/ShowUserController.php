<?php

declare(strict_types=1);

namespace App\Controller\Admin\User;

use App\Controller\BaseController;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted(User::ROLE_ADMIN)]
#[Route(
    'admin/user/{uuid}',
    name: 'app_admin_user_show',
    requirements: ['uuid' => self::UUID_REGEX],
    methods: Request::METHOD_GET
)]
final class ShowUserController extends BaseController
{
    public function __invoke(string $uuid, Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = $entityManager->getRepository(User::class)->find($uuid);

        if (null === $user) {
            throw $this->createNotFoundException();
        }

        return $this->render('admin/user/show.html.twig', ['user' => $user]);
    }
}