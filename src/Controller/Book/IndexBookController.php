<?php

declare(strict_types=1);

namespace App\Controller\Book;

use App\Controller\BaseController;
use App\Entity\History;
use App\Entity\Tag;
use App\Type\State;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/book', name: 'app_book_index', methods: Request::METHOD_GET)]
final class IndexBookController extends BaseController
{
    public function __invoke(EntityManagerInterface $entityManager): Response
    {
        if (null !== $this->getUser()) {
            $histories = $entityManager
                ->getRepository(History::class)
                ->findBy(['user' => $this->getUser()], ['uuid' => 'DESC'], limit: 5);
        }

        return $this->render('book/index.html.twig', [
            'histories' => $histories ?? null,
            'tags' => $entityManager->getRepository(Tag::class)->findAllPublished(),
        ]);
    }
}
