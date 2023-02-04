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
use Symfony\Component\Uid\UuidV6;

final class ChapterControllerTest extends WebTestCase
{
    /**
     * @param array<string, string> $chapterFormData
     *
     * @dataProvider provideGoodChapterData
     *
     * @test
     */
    public function shouldCreateChapter(array $chapterFormData): void
    {
        $client = self::createClient();
        /** @var Book $book */
        $book = self::getContainer()->get(BookRepository::class)->findOneBy(['title' => 'Title fixture 1']);
        $client->request(Request::METHOD_GET, '/author/book/'.$book->getUuid().'/chapter/create');
        $client->submitForm('Create', $chapterFormData);

        /** @var Chapter $chapter */
        $chapter = self::getContainer()->get(ChapterRepository::class)->findOneBy([
            'book' => $book->getUuid(),
            'title' => $chapterFormData['chapter[title]'],
        ]);

        self::assertResponseStatusCodeSame(Response::HTTP_FOUND);
        self::assertInstanceOf(UuidV6::class, $chapter->getUuid());
    }

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
     * @test
     */
    public function shouldDeleteChapter(): void
    {
        $client = self::createClient();
        /** @var Book $book */
        $book = self::getContainer()->get(BookRepository::class)->findOneBy(['title' => 'Title fixture 1']);
        $chapterRepository = self::getContainer()->get(ChapterRepository::class);

        /** @var array<array-key, Chapter> $chapters */
        $chapters = $chapterRepository->findBy(['book' => $book]);
        $client->request(Request::METHOD_GET, '/author/book/'.$book->getUuid().'/chapter');

        $client->submitForm('Delete');
        $crawler = $client->followRedirect();

        self::assertResponseStatusCodeSame(Response::HTTP_OK);
        self::assertCount(count($chapters) - 1, $crawler->filter('main a'));
    }

    /**
     * @param array<string, string> $chapterFormData
     *
     * @dataProvider provideBadChapterData
     *
     * @test
     */
    public function shouldNotCreateChapterAndDisplayGoodErrorMessage(array $chapterFormData, string $errorMessage): void
    {
        $client = self::createClient();
        /** @var Book $book */
        $book = self::getContainer()->get(BookRepository::class)->findOneBy(['title' => 'Title fixture 1']);
        $chapterRepository = self::getContainer()->get(ChapterRepository::class);
        $countChapter = count($chapterRepository->findBy(['book' => $book->getUuid()]));

        $client->request(Request::METHOD_GET, '/author/book/'.$book->getUuid().'/chapter/create');
        $client->submitForm('Create', $chapterFormData);

        self::assertResponseStatusCodeSame(Response::HTTP_UNPROCESSABLE_ENTITY);
        self::assertCount($countChapter, $chapterRepository->findBy(['book' => $book->getUuid()]));
        self::assertSelectorTextSame('main ul > li', $errorMessage);
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
    public function provideGoodChapterData(): \Generator
    {
        yield [
            ['chapter[title]' => 'Chapter 10'],
        ];
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
        yield [Request::METHOD_GET, '/1ed22f9f-8793-6c00-ad9e-1d77bf6a790b/chapter/create'];
        yield [Request::METHOD_POST, '/1ed22f9f-8793-6c00-ad9e-1d77bf6a790b/chapter/create'];
    }

    /**
     * @return \Generator<array<array-key, array<string, string>|string>>
     */
    public function provideBadChapterData(): \Generator
    {
        yield [['chapter[title]' => ''], 'This value should not be blank.'];
    }
}
