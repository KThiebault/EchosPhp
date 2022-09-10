<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Book;
use App\Entity\Chapter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class BookController extends AbstractController
{
    #[Route('/book', name: 'app_book_index', methods: Request::METHOD_GET)]
    public function index(EntityManagerInterface $entityManager): Response
    {
        return $this->render('book/index.html.twig', [
            'books' => $entityManager->getRepository(Book::class)->findBy([], limit: 12),
        ]);
    }

    #[Route(
        '/book/{uuid}',
        name: 'app_book_show',
        requirements: ['uuid' => '^[0-9a-fA-F]{8}\b-[0-9a-fA-F]{4}\b-[0-9a-fA-F]{4}\b-[0-9a-fA-F]{4}\b-[0-9a-fA-F]{12}$'],
        methods: Request::METHOD_GET
    )]
    public function show(string $uuid, EntityManagerInterface $entityManager): Response
    {
        $book = $entityManager->getRepository(Book::class)->find(['uuid' => $uuid]);

        if (null === $book) {
            throw $this->createNotFoundException();
        }

        return $this->render('book/show.html.twig', [
           'chapters' => $entityManager->getRepository(Chapter::class)->findBy(['book' => $book]),
        ]);
    }
}
