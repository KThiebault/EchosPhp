<?php

declare(strict_types=1);

namespace App\Controller\Author\Chapter;

use App\Controller\BaseController;
use App\Entity\Book;
use App\Entity\Chapter;
use App\Form\ChapterType;
use App\Security\BookVoter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
#[Route(
    path: '/author/book/{book_uuid}/chapter/create',
    name: 'app_author_chapter_create',
    requirements: ['book_uuid' => self::UUID_REGEX],
    methods: [Request::METHOD_GET, Request::METHOD_POST]
)]
final class CreateChapterController extends BaseController
{
    public function __invoke(string $book_uuid, Request $request, EntityManagerInterface $entityManager): Response
    {
        $book = $entityManager->getRepository(Book::class)->find($book_uuid);

        if (null === $book) {
            throw $this->createNotFoundException();
        }

        $this->denyAccessUnlessGranted(BookVoter::VIEW, $book);

        $chapter = new Chapter();
        $chapterFrom = $this->createForm(ChapterType::class, $chapter)->handleRequest($request);

        if ($chapterFrom->isSubmitted() && $chapterFrom->isValid()) {
            $chapter->setBook($book);
            $entityManager->persist($chapter);
            $entityManager->flush();

            return $this->redirectToRoute('app_author_chapter_index', ['book_uuid' => $book->getUuid()]);
        }

        return $this->render('author/chapter/create.html.twig', ['chapter_form' => $chapterFrom]);
    }
}
