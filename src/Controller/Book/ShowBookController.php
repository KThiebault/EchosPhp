<?php

declare(strict_types=1);

namespace App\Controller\Book;

use App\Controller\BaseController;
use App\Entity\Book;
use App\Entity\Chapter;
use App\Entity\History;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/book/{book_uuid}', name: 'app_book_show', requirements: ['book_uuid' => self::UUID_REGEX], methods: Request::METHOD_GET)]
final class ShowBookController extends BaseController
{
    public function __invoke(string $book_uuid, EntityManagerInterface $entityManager): Response
    {
        $book = $entityManager->getRepository(Book::class)->find(['uuid' => $book_uuid]);

        if (null === $book) {
            throw $this->createNotFoundException();
        }

        if (null !== $this->getUser()) {
            $history = $entityManager->getRepository(History::class)->findOneBy(['book' => $book, 'user' => $this->getUser()]);

            if (null !== $history) {
                return $this->redirectToRoute('app_book_read', [
                    'book_uuid' => $history->getBook()->getUuid(),
                    'chapter_uuid' => $history->getChapter()->getUuid(),
                ]);
            }
        }

        return $this->render('book/show.html.twig', [
            'book' => $book,
            'chapters' => $entityManager->getRepository(Chapter::class)->findBy(['book' => $book]),
        ]);
    }
}
