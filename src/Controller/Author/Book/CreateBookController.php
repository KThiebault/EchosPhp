<?php

declare(strict_types=1);

namespace App\Controller\Author\Book;

use App\Controller\BaseController;
use App\Entity\Book;
use App\Form\BookType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
#[Route('/author/book/create', name: 'app_author_book_create', methods: [Request::METHOD_GET, Request::METHOD_POST])]
final class CreateBookController extends BaseController
{
    public function __invoke(Request $request, EntityManagerInterface $entityManager): Response
    {
        $book = new Book();
        $bookForm = $this->createForm(BookType::class, $book)->handleRequest($request);

        if ($bookForm->isSubmitted() && $bookForm->isValid()) {
            $entityManager->persist($book);
            $entityManager->flush();

            return $this->redirectToRoute('app_author_book_index');
        }

        return $this->render('author/book/create.html.twig', ['book_form' => $bookForm]);
    }
}
