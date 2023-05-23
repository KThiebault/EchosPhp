<?php

declare(strict_types=1);

namespace App\Controller\Author\Book;

use App\Controller\BaseController;
use App\Entity\Book;
use App\Form\BookType;
use App\Security\BookVoter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
#[Route(
    'author/book/update/{book_uuid}',
    name: 'app_author_book_update',
    requirements: ['book_uuid' => self::UUID_REGEX],
    methods: [Request::METHOD_GET, Request::METHOD_POST]
)]
final class UpdateBookController extends BaseController
{
    public function __invoke(string $book_uuid, Request $request, EntityManagerInterface $entityManager): Response
    {
        $book = $entityManager->getRepository(Book::class)->find($book_uuid);

        if (null === $book) {
            throw $this->createNotFoundException();
        }

        $this->denyAccessUnlessGranted(BookVoter::VIEW, $book);
        $bookForm = $this->createForm(BookType::class, $book)->handleRequest($request);

        if ($bookForm->isSubmitted() && $bookForm->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', sprintf('%s has been updated.', $book->getTitle()));
            return $this->redirectToRoute('app_author_book_update', ['book_uuid' => $book_uuid]);
        }

        return $this->render('author/book/update.html.twig', [
            'book' => $book,
            'book_form' => $bookForm,
        ]);
    }
}
