<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Tag;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class TagController extends AbstractController
{
    #[Route(
        '/tag/{tag_uuid}',
        name: 'app_tag_show',
        requirements: ['tag_uuid' => '^[0-9a-fA-F]{8}\b-[0-9a-fA-F]{4}\b-[0-9a-fA-F]{4}\b-[0-9a-fA-F]{4}\b-[0-9a-fA-F]{12}$'],
        methods: Request::METHOD_GET
    )]
    public function show(string $tag_uuid, EntityManagerInterface $entityManager): Response
    {
        $tag = $entityManager->getRepository(Tag::class)->find(['uuid' => $tag_uuid]);

        if (null === $tag) {
            throw $this->createNotFoundException();
        }

        return $this->render('tag/show.html.twig', ['tag' => $tag]);
    }
}
