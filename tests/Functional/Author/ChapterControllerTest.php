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
     * @test
     */
    public function shouldListChapterForABook(): void
    {
        $client = self::createClient();
        /** @var Book $book */
        $book = self::getContainer()->get(BookRepository::class)->findOneBy(['title' => 'Title fixture 1']);
        $chapters = self::getContainer()->get(ChapterRepository::class)->findBy(['book' => $book->getUuid()]);
        $crawler = $client->request(Request::METHOD_GET, $book->getUuid().'/chapter');

        self::assertResponseIsSuccessful();
        self::assertCount(count($chapters), $crawler->filter('div'));
    }

    /**
     * @param array<string, string> $chapterFormData
     * @dataProvider provideGoodChapterData
     * @test
     */
    public function shouldCreateChapter(array $chapterFormData): void
    {
        $client = self::createClient();
        /** @var Book $book */
        $book = self::getContainer()->get(BookRepository::class)->findOneBy(['title' => 'Title fixture 1']);
        $client->request(Request::METHOD_GET, $book->getUuid().'/chapter/create');
        $client->submitForm('Create', $chapterFormData);

        /** @var Chapter $chapter */
        $chapter = self::getContainer()->get(ChapterRepository::class)->findOneBy([
            'book' => $book->getUuid(),
            'title' => $chapterFormData['chapter[title]'],
        ]);

        self::assertResponseStatusCodeSame(Response::HTTP_OK);
        self::assertInstanceOf(UuidV6::class, $chapter->getUuid());
    }

    /**
     * @dataProvider provideBadChapterUrl
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
            ['chapter[title]' => 'test'],
        ];
    }

    /**
     * @return \Generator<array<array-key, string>>
     */
    public function provideBadChapterUrl(): \Generator
    {
        yield [Request::METHOD_GET, '/1ed22f9f-8793-6c00-ad9e-1d77bf6a790b/chapter'];
        yield [Request::METHOD_GET, '/1ed22f9f-8793-6c00-ad9e-1d77bf6a790b/chapter/create'];
        yield [Request::METHOD_POST, '/1ed22f9f-8793-6c00-ad9e-1d77bf6a790b/chapter/create'];
    }
}
