<?php

declare(strict_types=1);

namespace App\Controller\Author\Chapter;

use App\Controller\BaseController;
use App\Entity\Book;
use App\Entity\Chapter;
use App\Entity\User;
use App\Security\BookVoter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted(User::ROLE_USER)]
#[Route(
    path: '/author/book/{book_uuid}/chapter',
    name: 'app_author_chapter_index',
    requirements: ['book_uuid' => self::UUID_REGEX],
    methods: Request::METHOD_GET
)]
final class IndexChapterController extends BaseController
{
    public function __invoke(string $book_uuid, EntityManagerInterface $entityManager): Response
    {
        $book = $entityManager->getRepository(Book::class)->find($book_uuid);

        if (null === $book) {
            throw $this->createNotFoundException();
        }

        $this->denyAccessUnlessGranted(BookVoter::VIEW, $book);

        return $this->render('author/chapter/index.html.twig', [
            'book' => $book,
            'chapters' => $entityManager->getRepository(Chapter::class)->findBy(['book' => $book_uuid]),
        ]);
    }
}
