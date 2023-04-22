<?php

declare(strict_types=1);

namespace App\Controller\Tag;

use App\Controller\BaseController;
use App\Entity\Tag;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/tag/{tag_uuid}', name: 'app_tag_show', requirements: ['tag_uuid' => self::UUID_REGEX], methods: Request::METHOD_GET)]
final class ShowTagController extends BaseController
{
    public function __invoke(string $tag_uuid, EntityManagerInterface $entityManager): Response
    {
        $tag = $entityManager->getRepository(Tag::class)->findPublished($tag_uuid);

        if (null === $tag) {
            throw $this->createNotFoundException();
        }

        return $this->render('tag/show.html.twig', ['tag' => $tag]);
    }
}
