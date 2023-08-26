<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\Tag;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin')]
final class TagController extends AbstractController
{
    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }

    #[Route(
        path: '/tag/delete/{uuid}',
        name: 'app_admin_tag_delete',
        requirements: ['uuid' => '^[0-9a-fA-F]{8}\b-[0-9a-fA-F]{4}\b-[0-9a-fA-F]{4}\b-[0-9a-fA-F]{4}\b-[0-9a-fA-F]{12}$'],
        methods: Request::METHOD_POST
    )]
    public function delete(string $uuid, Request $request): Response
    {
        if (false === $this->isCsrfTokenValid('delete'.$uuid, (string) $request->request->get('csrf_token'))) {
            return $this->redirectToRoute('app_admin_tag_index');
        }

        $tag = $this->entityManager->getRepository(Tag::class)->find($uuid);

        if (null === $tag) {
            throw $this->createNotFoundException();
        }

        $this->entityManager->remove($tag);
        $this->entityManager->flush();

        return $this->redirectToRoute('app_admin_tag_index');
    }
}
