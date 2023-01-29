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
    public function read(string $book_uuid, string $chapter_uuid, EntityManagerInterface $entityManager): Response
    {
        $book = $entityManager->getRepository(Book::class)->find(['uuid' => $book_uuid]);

        if (null === $book) {
            throw $this->createNotFoundException();
        }

        $chapter = null;
        $nextChapter = null;
        /** @var array<array-key, Chapter> $chapters */
        $chapters = $entityManager->getRepository(Chapter::class)->findBy(['book' => $book->getUuid()]);

        foreach ($chapters as $key => $value) {
            if ($value->getUuid()->__toString() === $chapter_uuid) {
                $chapter = $value;
                $nextChapter = array_key_exists($key+1, $chapters) ? $chapters[$key+1] : null;
            }
        }

        if (null === $chapter) {
            throw $this->createNotFoundException();
        }

        return $this->render('book/read.html.twig', [
            'chapters' => $chapters,
            'next_chapter' => $nextChapter,
            'selected_chapter' => $chapter,
            'book_uuid' => $book->getUuid(),
            'pages' => $entityManager->getRepository(Page::class)->findBy(['chapter' => $chapter]),
        ]);
    }
}
