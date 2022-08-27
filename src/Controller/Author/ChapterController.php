<?php

declare(strict_types=1);

namespace App\Controller\Author;

use App\Entity\Book;
use App\Entity\Chapter;
use App\Form\ChapterType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class ChapterController extends AbstractController
{
    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }

    #[Route(
        path: '/{uuid}/chapter',
        name: 'app_chapter_index',
        requirements: ['uuid' => '^[0-9a-fA-F]{8}\b-[0-9a-fA-F]{4}\b-[0-9a-fA-F]{4}\b-[0-9a-fA-F]{4}\b-[0-9a-fA-F]{12}$'],
        methods: Request::METHOD_GET
    )]
    public function index(string $uuid): Response
    {
        $book = $this->entityManager->getRepository(Book::class)->find($uuid);

        if (null === $book) {
            throw $this->createNotFoundException();
        }

        return $this->render('author/chapter/index.html.twig', [
            'chapters' => $this->entityManager->getRepository(Chapter::class)->findBy(['book' => $uuid]),
        ]);
    }

    #[Route(
        path: '/{uuid}/chapter/create',
        name: 'app_chapter_create',
        requirements: ['uuid' => '^[0-9a-fA-F]{8}\b-[0-9a-fA-F]{4}\b-[0-9a-fA-F]{4}\b-[0-9a-fA-F]{4}\b-[0-9a-fA-F]{12}$'],
        methods: [Request::METHOD_GET, Request::METHOD_POST]
    )]
    public function create(string $uuid, Request $request): Response
    {
        $book = $this->entityManager->getRepository(Book::class)->find($uuid);

        if (null === $book) {
            throw $this->createNotFoundException();
        }

        $chapter = new Chapter();
        $chapterFrom = $this->createForm(ChapterType::class, $chapter)->handleRequest($request);

        if ($chapterFrom->isSubmitted() && $chapterFrom->isValid()) {
            $chapter->setBook($book);
            $this->entityManager->persist($chapter);
            $this->entityManager->flush();
        }

        return $this->render('author/chapter/create.html.twig', ['chapter_form' => $chapterFrom->createView()]);
    }
}
