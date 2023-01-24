<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Book;
use App\Entity\Chapter;
use App\Entity\Page;
use App\Entity\Tag;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class BookController extends AbstractController
{
    #[Route('/book', name: 'app_book_index', methods: Request::METHOD_GET)]
    public function index(EntityManagerInterface $entityManager): Response
    {
        return $this->render('book/index.html.twig', [
            'tags' => $entityManager->getRepository(Tag::class)->findBy([], limit: 12),
        ]);
    }

    #[Route(
        '/book/{book_uuid}',
        name: 'app_book_show',
        requirements: ['book_uuid' => '^[0-9a-fA-F]{8}\b-[0-9a-fA-F]{4}\b-[0-9a-fA-F]{4}\b-[0-9a-fA-F]{4}\b-[0-9a-fA-F]{12}$'],
        methods: Request::METHOD_GET
    )]
    public function show(string $book_uuid, EntityManagerInterface $entityManager): Response
    {
        $book = $entityManager->getRepository(Book::class)->find(['uuid' => $book_uuid]);

        if (null === $book) {
            throw $this->createNotFoundException();
        }

        return $this->render('book/show.html.twig', [
            'book' => $book,
            'chapters' => $entityManager->getRepository(Chapter::class)->findBy(['book' => $book]),
        ]);
    }

    #[Route(
        '/book/{book_uuid}/chapter/{chapter_uuid}',
        name: 'app_book_read',
        requirements: [
            'book_uuid' => '^[0-9a-fA-F]{8}\b-[0-9a-fA-F]{4}\b-[0-9a-fA-F]{4}\b-[0-9a-fA-F]{4}\b-[0-9a-fA-F]{12}$',
            'chapter_uuid' => '^[0-9a-fA-F]{8}\b-[0-9a-fA-F]{4}\b-[0-9a-fA-F]{4}\b-[0-9a-fA-F]{4}\b-[0-9a-fA-F]{12}$',
        ],
        methods: Request::METHOD_GET
    )]
    public function read(string $chapter_uuid, EntityManagerInterface $entityManager): Response
    {
        $chapter = $entityManager->getRepository(Chapter::class)->find(['uuid' => $chapter_uuid]);

        if (null === $chapter) {
            throw $this->createNotFoundException();
        }

        return $this->render('book/read.html.twig', [
            'pages' => $entityManager->getRepository(Page::class)->findBy(['chapter' => $chapter]),
        ]);
    }
}
