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
#[Route('admin/tag', name: 'app_admin_tag_index', methods: Request::METHOD_GET)]
final class IndexTagController extends AbstractController
{
    public function __invoke(Request $request, EntityManagerInterface $entityManager): Response
    {
        return $this->render(
            'admin/tag/index.html.twig',
            ['tags' => $entityManager->getRepository(Tag::class)->findAll()]
        );
    }
}