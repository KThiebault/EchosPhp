<?php

declare(strict_types=1);

namespace App\Controller\Admin\Tag;

use App\Entity\Tag;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted(User::ROLE_ADMIN)]
#[Route(
    'admin/tag/delete/{uuid}',
    name: 'app_admin_tag_delete',
    requirements: ['uuid' => '^[0-9a-fA-F]{8}\b-[0-9a-fA-F]{4}\b-[0-9a-fA-F]{4}\b-[0-9a-fA-F]{4}\b-[0-9a-fA-F]{12}$'],
    methods: Request::METHOD_POST
)]
final class DeleteTagController extends AbstractController
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