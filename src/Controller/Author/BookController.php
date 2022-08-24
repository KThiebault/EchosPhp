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

final class BookController extends AbstractController
{
    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }

    #[Route(path: '/book', name: 'app_book_index', methods: Request::METHOD_GET)]
    public function index(): Response
    {
        return $this->render('author/book/index.html.twig', [
            'books' => $this->entityManager->getRepository(Book::class)->findAll(),
        ]);
    }

    #[Route(path: '/book/create', name: 'app_book_create', methods: [Request::METHOD_GET, Request::METHOD_POST])]
    public function create(Request $request): Response
    {
        $book = new Book();
        $bookFrom = $this->createForm(BookType::class, $book)->handleRequest($request);

        if ($bookFrom->isSubmitted() && $bookFrom->isValid()) {
            $this->entityManager->persist($book);
            $this->entityManager->flush();
        }

        return $this->render('author/book/create.html.twig', ['book_form' => $bookFrom->createView()]);
    }

    #[Route(
        path: '/book/update/{uuid}',
        name: 'app_book_update',
        requirements: ['uuid' => '^[0-9a-fA-F]{8}\b-[0-9a-fA-F]{4}\b-[0-9a-fA-F]{4}\b-[0-9a-fA-F]{4}\b-[0-9a-fA-F]{12}$'],
        methods: [Request::METHOD_GET, Request::METHOD_POST]
    )]
    public function update(string $uuid, Request $request): Response
    {
        $book = $this->entityManager->getRepository(Book::class)->find($uuid);

        if (null === $book) {
            throw $this->createNotFoundException();
        }

        $bookFrom = $this->createForm(BookType::class, $book)->handleRequest($request);

        if ($bookFrom->isSubmitted() && $bookFrom->isValid()) {
            $this->entityManager->flush();

            return $this->redirectToRoute('app_book_update', ['uuid' => $uuid]);
        }

        return $this->render('author/book/update.html.twig', ['book_form' => $bookFrom->createView()]);
    }
}
