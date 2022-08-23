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
    #[Route(path: '/book/create', name: 'app_book_create', methods: [Request::METHOD_GET, Request::METHOD_POST])]
    public function create(Request $request, EntityManagerInterface $entityManager): Response
    {
        $book = new Book();
        $bookFrom = $this->createForm(BookType::class, $book)->handleRequest($request);

        if ($bookFrom->isSubmitted() && $bookFrom->isValid()) {
            $entityManager->persist($book);
            $entityManager->flush();
        }

        return $this->render('book/create.html.twig', ['book_form' => $bookFrom->createView()]);
    }
}
