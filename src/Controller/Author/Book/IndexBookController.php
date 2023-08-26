<?php

declare(strict_types=1);

namespace App\Controller\Author\Book;

use App\Controller\BaseController;
use App\Entity\Book;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted(User::ROLE_USER)]
#[Route('/author/book', name: 'app_author_book_index', methods: Request::METHOD_GET)]
final class IndexBookController extends BaseController
{
    public const PAGE_SIZE = 10;

    public function __invoke(EntityManagerInterface $entityManager, Request $request): Response
    {
        $repository = $entityManager->getRepository(Book::class);
        /** @var int $currentPage */
        $currentPage = $request->query->get('p', 1);
        $nextPage = $currentPage + 1;
        $previousPage = $currentPage - 1;
        $firstBook = ($currentPage - 1) * self::PAGE_SIZE;
        $paginatedBooks = $repository->findPaginatedBooksForAuthor($firstBook, self::PAGE_SIZE, $this->getUser());

        return $this->render('author/book/index.html.twig', [
            'books' => $paginatedBooks,
            'maxPage' => ceil($paginatedBooks->count() / self::PAGE_SIZE),
            'nextPage' => $nextPage,
            'previousPage' => $previousPage,
            'firstBook' => (($currentPage - 1) * self::PAGE_SIZE) + 1,
            'lastBook' => ($currentPage * self::PAGE_SIZE) > $paginatedBooks->count() ? $paginatedBooks->count() : $currentPage * self::PAGE_SIZE,
        ]);
    }
}
