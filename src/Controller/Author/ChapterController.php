<?php

declare(strict_types=1);

namespace App\Controller\Author;

use App\Entity\Book;
use App\Entity\Chapter;
use App\Entity\Page;
use App\Form\ChapterType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\ClickableInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class ChapterController extends AbstractController
{
    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }

    #[Route(
        path: 'book/{book_uuid}/chapter',
        name: 'app_chapter_index',
        requirements: ['book_uuid' => '^[0-9a-fA-F]{8}\b-[0-9a-fA-F]{4}\b-[0-9a-fA-F]{4}\b-[0-9a-fA-F]{4}\b-[0-9a-fA-F]{12}$'],
        methods: Request::METHOD_GET
    )]
    public function index(string $book_uuid): Response
    {
        $book = $this->entityManager->getRepository(Book::class)->find($book_uuid);

        if (null === $book) {
            throw $this->createNotFoundException();
        }

        return $this->render('author/chapter/index.html.twig', [
            'book_uuid' => $book->getUuid(),
            'chapters' => $this->entityManager->getRepository(Chapter::class)->findBy(['book' => $book_uuid]),
        ]);
    }

    #[Route(
        path: 'book/{book_uuid}/chapter/create',
        name: 'app_chapter_create',
        requirements: ['book_uuid' => '^[0-9a-fA-F]{8}\b-[0-9a-fA-F]{4}\b-[0-9a-fA-F]{4}\b-[0-9a-fA-F]{4}\b-[0-9a-fA-F]{12}$'],
        methods: [Request::METHOD_GET, Request::METHOD_POST]
    )]
    public function create(string $book_uuid, Request $request): Response
    {
        $book = $this->entityManager->getRepository(Book::class)->find($book_uuid);

        if (null === $book) {
            throw $this->createNotFoundException();
        }

        $chapter = new Chapter();
        $chapterFrom = $this->createForm(ChapterType::class, $chapter)->handleRequest($request);

        if ($chapterFrom->isSubmitted() && $chapterFrom->isValid()) {
            $chapter->setBook($book);
            $this->entityManager->persist($chapter);
            $this->entityManager->flush();

            return $this->redirectToRoute('app_chapter_index', [
                'book_uuid' => $book->getUuid(),
            ]);
        }

        return $this->render('author/chapter/create.html.twig', ['chapter_form' => $chapterFrom->createView()]);
    }

    #[Route(
        path: 'book/{book_uuid}/chapter/update/{chapter_uuid}',
        name: 'app_chapter_update',
        requirements: [
            'book_uuid' => '^[0-9a-fA-F]{8}\b-[0-9a-fA-F]{4}\b-[0-9a-fA-F]{4}\b-[0-9a-fA-F]{4}\b-[0-9a-fA-F]{12}$',
            'chapter_uuid' => '^[0-9a-fA-F]{8}\b-[0-9a-fA-F]{4}\b-[0-9a-fA-F]{4}\b-[0-9a-fA-F]{4}\b-[0-9a-fA-F]{12}$',
        ],
        methods: [Request::METHOD_GET, Request::METHOD_POST]
    )]
    public function update(string $book_uuid, string $chapter_uuid, Request $request): Response
    {
        $book = $this->entityManager->getRepository(Book::class)->find($book_uuid);
        $chapter = $this->entityManager->getRepository(Chapter::class)->find($chapter_uuid);

        if (null === $book || null === $chapter) {
            throw $this->createNotFoundException();
        }
        $chapterFrom = $this->createForm(ChapterType::class, $chapter)->handleRequest($request);

        if ($chapterFrom->isSubmitted() && $chapterFrom->isValid()) {
            /** @var ClickableInterface $addPageBtn */
            $addPageBtn = $chapterFrom->get('addPage');
            if ($addPageBtn->isClicked()) {
                $chapter->addPage(new Page());
            }

            $this->entityManager->flush();

            return $this->redirectToRoute('app_chapter_update', [
                'book_uuid' => $book->getUuid(),
                'chapter_uuid' => $chapter->getUuid(),
            ]);
        }

        return $this->render('author/chapter/update.html.twig', ['chapter_form' => $chapterFrom->createView()]);
    }
}
