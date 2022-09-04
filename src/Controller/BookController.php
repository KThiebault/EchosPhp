<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Book;
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
}
