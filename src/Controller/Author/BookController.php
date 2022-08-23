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
        return $this->render('book/index.html.twig', ['books' => $this->entityManager->getRepository(Book::class)->findAll()]);
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

        return $this->render('book/create.html.twig', ['book_form' => $bookFrom->createView()]);
    }
}
