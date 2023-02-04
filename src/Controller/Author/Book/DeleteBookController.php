<?php

declare(strict_types=1);

namespace App\Controller\Author\Book;

use App\Controller\BaseController;
use App\Entity\Book;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
#[Route(
    'author/book/delete/{book_uuid}',
    name: 'app_author_book_delete',
    requirements: ['book_uuid' => self::UUID_REGEX],
    methods: Request::METHOD_POST
)]
final class DeleteBookController extends BaseController
{
    public function __invoke(string $book_uuid, Request $request, EntityManagerInterface $entityManager): Response
    {
        if (false === $this->isCsrfTokenValid('delete'.$book_uuid, (string) $request->request->get('csrf_token'))) {
            return $this->redirectToRoute('app_author_book_index');
        }

        $book = $entityManager->getRepository(Book::class)->find($book_uuid);

        if (null === $book) {
            throw $this->createNotFoundException();
        }

        $entityManager->remove($book);
        $entityManager->flush();

        return $this->redirectToRoute('app_author_book_index');
    }
}
