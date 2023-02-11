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
#[Route('/author/book', name: 'app_author_book_index', methods: Request::METHOD_GET)]
final class IndexBookController extends BaseController
{
    public function __invoke(EntityManagerInterface $entityManager): Response
    {
        return $this->render('author/book/index.html.twig', [
            'books' => $entityManager->getRepository(Book::class)->findBy(['author' => $this->getUser()], ['createdAt' => 'DESC']),
        ]);
    }
}
