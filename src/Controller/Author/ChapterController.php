<?php

declare(strict_types=1);

namespace App\Controller\Author;

use App\Entity\Chapter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class ChapterController extends AbstractController
{
    #[Route(
        path: '/{uuid}/chapter',
        name: 'app_chapter_index',
        requirements: ['uuid' => '^[0-9a-fA-F]{8}\b-[0-9a-fA-F]{4}\b-[0-9a-fA-F]{4}\b-[0-9a-fA-F]{4}\b-[0-9a-fA-F]{12}$'],
        methods: Request::METHOD_GET
    )]
    public function index(string $uuid, EntityManagerInterface $entityManager): Response
    {
        return $this->render('author/chapter/index.html.twig', [
            'chapters' => $entityManager->getRepository(Chapter::class)->findBy(['book' => $uuid]),
        ]);
    }
}
