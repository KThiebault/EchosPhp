<?php

declare(strict_types=1);

namespace App\Tests\Functional;

use App\Entity\Book;
use App\Entity\Chapter;
use App\Repository\BookRepository;
use App\Repository\ChapterRepository;
use App\Repository\PageRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class BookControllerTest extends WebTestCase
{
    /**
     * @test
     */
    public function shouldDisplaySevenTags(): void
    {
        $client = self::createClient();
        $crawler = $client->request(Request::METHOD_GET, '/book');

        self::assertResponseIsSuccessful();
        self::assertCount(6, $crawler->filter('main h2'));
    }

    /**
     * @test
     */
    public function shouldDisplayOneBookWithThisChapters(): void
    {
        $client = self::createClient();
        /** @var Book $book */
        $book = $client->getContainer()->get(BookRepository::class)->findOneBy(['title' => 'Title fixture 1']);
        $chapterCount = count($client->getContainer()->get(ChapterRepository::class)->findBy(['book' => $book]));

        $crawler = $client->request(Request::METHOD_GET, '/book/'.$book->getUuid());

        self::assertResponseIsSuccessful();
        self::assertCount($chapterCount, $crawler->filter('article'));
    }

    /**
     * @test
     */
    public function shouldThrowNotFoundExceptionIfBookIsNotFound(): void
    {
        $client = self::createClient();
        $client->catchExceptions(false);

        self::expectException(NotFoundHttpException::class);
        $client->request(Request::METHOD_GET, '/book/1ed22f9f-8793-6c00-ad9e-1d77bf6a790b');
    }

    /**
     * @test
     */
    public function shouldDisplayOneChapterWithThisPages(): void
    {
        $client = self::createClient();
        /** @var Book $book */
        $book = $client->getContainer()->get(BookRepository::class)->findOneBy(['title' => 'Title fixture 1']);
        /** @var Chapter $chapter */
        $chapter = $client->getContainer()->get(ChapterRepository::class)->findOneBy(['book' => $book]);
        $countPage = count($client->getContainer()->get(PageRepository::class)->findBy(['chapter' => $chapter]));

        $crawler = $client->request(Request::METHOD_GET, '/book/'.$book->getUuid().'/chapter/'.$chapter->getUuid());

        self::assertResponseIsSuccessful();
        self::assertCount($countPage, $crawler->filter('div.container'));
    }

    /**
     * @test
     */
    public function shouldThrowNotFoundExceptionIfChapterIsNotFound(): void
    {
        $client = self::createClient();
        $client->catchExceptions(false);

        self::expectException(NotFoundHttpException::class);
        $client->request(Request::METHOD_GET, '/book/1ed22f9f-8793-6c00-ad9e-1d77bf6a790b/chapter/1ed22f9f-8793-6c00-ad9e-1d77bf6a790b');
    }

    /**
     * @test
     */
    public function shouldDisplayNextChapter(): void
    {
        $client = self::createClient();
        /** @var Book $book */
        $book = $client->getContainer()->get(BookRepository::class)->findOneBy(['title' => 'Title fixture 1']);
        /** @var array<array-key, Chapter> $chapter */
        $chapters = $client->getContainer()->get(ChapterRepository::class)->findBy(['book' => $book]);
        $chapter = $chapters[count($chapters) - 2];

        $client->request(Request::METHOD_GET, '/book/'.$book->getUuid().'/chapter/'.$chapter->getUuid());

        self::assertResponseIsSuccessful();
        self::assertSelectorTextSame(
            'main div > a[href="/book/'.$book->getUuid().'/chapter/'.$chapters[count($chapters) - 1]->getUuid().'"]',
            'Next chapter'
        );
    }

    /**
     * @test
     */
    public function shouldDisplayAllChapters(): void
    {
        $client = self::createClient();
        /** @var Book $book */
        $book = $client->getContainer()->get(BookRepository::class)->findOneBy(['title' => 'Title fixture 1']);
        /** @var array<array-key, Chapter> $chapter */
        $chapters = $client->getContainer()->get(ChapterRepository::class)->findBy(['book' => $book]);

        $crawler = $client->request(Request::METHOD_GET, '/book/'.$book->getUuid().'/chapter/'.$chapters[0]->getUuid());

        self::assertCount(count($chapters), $crawler->filter('main div ul li'));
    }

    /**
     * @test
     */
    public function shouldUnderlineSelectedChapter(): void
    {
        $client = self::createClient();
        /** @var Book $book */
        $book = $client->getContainer()->get(BookRepository::class)->findOneBy(['title' => 'Title fixture 1']);
        /** @var array<array-key, Chapter> $chapter */
        $chapters = $client->getContainer()->get(ChapterRepository::class)->findBy(['book' => $book]);

        $client->request(Request::METHOD_GET, '/book/'.$book->getUuid().'/chapter/'.$chapters[2]->getUuid());

        self::assertSelectorTextSame('main div ul li a.underline', $chapters[2]->getTitle());
    }
}
