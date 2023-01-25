<?php

declare(strict_types=1);

namespace App\Controller\Author;

use App\Entity\Book;
use App\Form\BookType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/author')]
final class BookController extends AbstractController
{
    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }

    #[Route(path: '/book', name: 'app_author_book_index', methods: Request::METHOD_GET)]
    public function index(): Response
    {
        return $this->render('author/book/index.html.twig', [
            'books' => $this->entityManager->getRepository(Book::class)->findAll(),
        ]);
    }

    #[Route(path: '/book/create', name: 'app_author_book_create', methods: [Request::METHOD_GET, Request::METHOD_POST])]
    public function create(Request $request): Response
    {
        $book = new Book();
        $bookForm = $this->createForm(BookType::class, $book)->handleRequest($request);

        if ($bookForm->isSubmitted() && $bookForm->isValid()) {
            $this->entityManager->persist($book);
            $this->entityManager->flush();

            return $this->redirectToRoute('app_author_book_index');
        }

        return $this->render('author/book/create.html.twig', ['book_form' => $bookForm]);
    }

    #[Route(
        path: '/book/update/{uuid}',
        name: 'app_author_book_update',
        requirements: ['uuid' => '^[0-9a-fA-F]{8}\b-[0-9a-fA-F]{4}\b-[0-9a-fA-F]{4}\b-[0-9a-fA-F]{4}\b-[0-9a-fA-F]{12}$'],
        methods: [Request::METHOD_GET, Request::METHOD_POST]
    )]
    public function update(string $uuid, Request $request): Response
    {
        $book = $this->entityManager->getRepository(Book::class)->find($uuid);

        if (null === $book) {
            throw $this->createNotFoundException();
        }

        $bookForm = $this->createForm(BookType::class, $book)->handleRequest($request);

        if ($bookForm->isSubmitted() && $bookForm->isValid()) {
            $this->entityManager->flush();

            return $this->redirectToRoute('app_author_book_update', ['uuid' => $uuid]);
        }

        return $this->render('author/book/update.html.twig', ['book_form' => $bookForm]);
    }

    #[Route(
        path: '/book/delete/{uuid}',
        name: 'app_author_book_delete',
        requirements: ['uuid' => '^[0-9a-fA-F]{8}\b-[0-9a-fA-F]{4}\b-[0-9a-fA-F]{4}\b-[0-9a-fA-F]{4}\b-[0-9a-fA-F]{12}$'],
        methods: Request::METHOD_POST
    )]
    public function delete(string $uuid, Request $request): Response
    {
        if (false === $this->isCsrfTokenValid('delete'.$uuid, (string) $request->request->get('csrf_token'))) {
            return $this->redirectToRoute('app_author_book_index');
        }

        $book = $this->entityManager->getRepository(Book::class)->find($uuid);

        if (null === $book) {
            throw $this->createNotFoundException();
        }

        $this->entityManager->remove($book);
        $this->entityManager->flush();

        return $this->redirectToRoute('app_author_book_index');
    }
}
