<?php

declare(strict_types=1);

namespace App\Tests\Functional\Author;

use App\Entity\Book;
use App\Entity\Chapter;
use App\Repository\BookRepository;
use App\Repository\ChapterRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class ChapterControllerTest extends WebTestCase
{
    /**
     * @param array<string, string> $updateChapterFormData
     *
     * @dataProvider provideGoodUpdatedBookData
     *
     * @test
     */
    public function shouldUpdateChapter(array $updateChapterFormData): void
    {
        $client = self::createClient();
        /** @var Book $book */
        $book = self::getContainer()->get(BookRepository::class)->findOneBy(['title' => 'Title fixture 1']);
        /** @var Chapter $chapter */
        $chapter = self::getContainer()->get(ChapterRepository::class)->findOneBy(['title' => 'Chapter 1']);

        $crawler = $client->request(Request::METHOD_GET, '/author/book/'.$book->getUuid().'/chapter/update/'.$chapter->getUuid());

        $client->submitForm('Update', $updateChapterFormData);

        self::assertResponseStatusCodeSame(Response::HTTP_FOUND);
        self::assertCount(count($chapter->getPages()), $crawler->filter('textarea'));
        self::assertNotSame($updateChapterFormData['chapter[title]'], $book->getTitle());
    }

    /**
     * @test
     */
    public function shouldCreateAPage(): void
    {
        $client = self::createClient();
        /** @var Book $book */
        $book = self::getContainer()->get(BookRepository::class)->findOneBy(['title' => 'Title fixture 1']);
        $chapterRepository = self::getContainer()->get(ChapterRepository::class);

        /** @var Chapter $chapter */
        $chapter = $chapterRepository->findOneBy(['title' => 'Chapter 1']);
        $actualPages = count($chapter->getPages()) + 1;

        $client->request(Request::METHOD_GET, '/author/book/'.$book->getUuid().'/chapter/update/'.$chapter->getUuid());
        $client->submitForm('Add page');

        /** @var Chapter $chapter */
        $chapter = $chapterRepository->findOneBy(['title' => 'Chapter 1']);

        self::assertResponseStatusCodeSame(Response::HTTP_FOUND);
        self::assertCount($actualPages, $chapter->getPages());
    }

    /**
     * @dataProvider provideBadChapterUrl
     *
     * @test
     */
    public function shouldThrowNotFoundExceptionIfBookIsNotFound(string $method, string $url): void
    {
        $client = self::createClient();
        $client->catchExceptions(false);

        self::expectException(NotFoundHttpException::class);
        $client->request($method, $url);
    }

    /**
     * @return \Generator<array<array-key, array<string, string>>>
     */
    public function provideGoodUpdatedBookData(): \Generator
    {
        yield [
            ['chapter[title]' => 'Chapter'],
        ];
    }

    /**
     * @return \Generator<array<array-key, string>>
     */
    public function provideBadChapterUrl(): \Generator
    {
        yield [Request::METHOD_POST, '/1ed22f9f-8793-6c00-ad9e-1d77bf6a790b/chapter/create'];
    }
}
