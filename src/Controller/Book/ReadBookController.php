<?php

declare(strict_types=1);

namespace App\Controller\Book;

use App\Controller\BaseController;
use App\Entity\Book;
use App\Entity\Chapter;
use App\Entity\History;
use App\Entity\Page;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(
    '/book/{book_uuid}/chapter/{chapter_uuid}',
    name: 'app_book_read',
    requirements: ['book_uuid' => self::UUID_REGEX, 'chapter_uuid' => self::UUID_REGEX],
    methods: Request::METHOD_GET
)]
final class ReadBookController extends BaseController
{
    public function __invoke(string $book_uuid, string $chapter_uuid, EntityManagerInterface $entityManager): Response
    {
        $book = $entityManager->getRepository(Book::class)->find(['uuid' => $book_uuid]);

        if (null === $book) {
            throw $this->createNotFoundException();
        }

        $chapter = null;
        $nextChapter = null;
        $chapters = $entityManager->getRepository(Chapter::class)->findBy(['book' => $book->getUuid()]);

        foreach ($chapters as $key => $value) {
            if ($value->getUuid()->__toString() === $chapter_uuid) {
                $chapter = $value;
                $nextChapter = array_key_exists($key + 1, $chapters) ? $chapters[$key + 1] : null;
            }
        }

        if (null === $chapter) {
            throw $this->createNotFoundException();
        }

        if (null !== $this->getUser()) {
            $history = $entityManager->getRepository(History::class)->findOneBy(['book' => $book, 'user' => $this->getUser()]);

            if (null !== $history) {
                $history->setChapter($chapter);
            } else {
                $history = new History();
                $history->setUser($this->getUser());
                $history->setBook($book);
                $history->setChapter($chapter);

                $entityManager->persist($history);
            }

            $entityManager->flush();
        }

        return $this->render('book/read.html.twig', [
            'chapters' => $chapters,
            'next_chapter' => $nextChapter,
            'current_chapter' => $chapter,
            'book_uuid' => $book->getUuid(),
            'pages' => $entityManager->getRepository(Page::class)->findBy(['chapter' => $chapter]),
        ]);
    }
}
