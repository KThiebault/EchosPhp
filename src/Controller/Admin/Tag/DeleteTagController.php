<?php

declare(strict_types=1);

namespace App\Controller\Admin\Tag;

use App\Controller\BaseController;
use App\Entity\Tag;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted(User::ROLE_ADMIN)]
#[Route(
    'admin/tag/delete/{uuid}',
    name: 'app_admin_tag_delete',
    requirements: ['uuid' => self::UUID_REGEX],
    methods: Request::METHOD_POST
)]
final class DeleteTagController extends BaseController
{
    public function __invoke(string $uuid, Request $request, EntityManagerInterface $entityManager): Response
    {
        if (false === $this->isCsrfTokenValid('delete'.$uuid, (string) $request->request->get('csrf_token'))) {
            return $this->redirectToRoute('app_admin_tag_index');
        }

        $tag = $entityManager->getRepository(Tag::class)->find($uuid);

        if (null === $tag) {
            throw $this->createNotFoundException();
        }

        $entityManager->remove($tag);
        $entityManager->flush();

        return $this->redirectToRoute('app_admin_tag_index');
    }
}