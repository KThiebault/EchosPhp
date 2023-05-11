<?php

declare(strict_types=1);

namespace App\Controller\Author\Chapter;

use App\Controller\BaseController;
use App\Entity\Book;
use App\Entity\Chapter;
use App\Entity\Page;
use App\Form\ChapterType;
use App\Security\BookVoter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\ClickableInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
#[Route(
    path: '/author/book/{book_uuid}/chapter/update/{chapter_uuid}',
    name: 'app_author_chapter_update',
    requirements: ['book_uuid' => self::UUID_REGEX, 'chapter_uuid' => self::UUID_REGEX],
    methods: [Request::METHOD_GET, Request::METHOD_POST]
)]
final class UpdateChapterController extends BaseController
{
    public function __invoke(string $book_uuid, string $chapter_uuid, Request $request, EntityManagerInterface $entityManager): Response
    {
        $book = $entityManager->getRepository(Book::class)->find($book_uuid);
        $chapter = $entityManager->getRepository(Chapter::class)->find($chapter_uuid);

        if (null === $book || null === $chapter) {
            throw $this->createNotFoundException();
        }

        $this->denyAccessUnlessGranted(BookVoter::VIEW, $book);
        $chapterFrom = $this->createForm(ChapterType::class, $chapter)->handleRequest($request);

        if ($chapterFrom->isSubmitted() && $chapterFrom->isValid()) {
            /** @var ClickableInterface $addPageBtn */
            $addPageBtn = $chapterFrom->get('addPage');
            if ($addPageBtn->isClicked()) {
                $chapter->addPage(new Page());
            }

            $entityManager->flush();

            return $this->redirectToRoute('app_author_chapter_update', [
                'book_uuid' => $book->getUuid(),
                'chapter_uuid' => $chapter->getUuid(),
            ]);
        }

        return $this->render('author/chapter/update.html.twig', ['chapter_form' => $chapterFrom]);
    }
}
