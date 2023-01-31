<?php

declare(strict_types=1);

namespace App\Tests\Functional\Book;

use App\Entity\Book;
use App\Entity\Chapter;
use App\Entity\History;
use App\Entity\User;
use App\Repository\BookRepository;
use App\Repository\ChapterRepository;
use App\Repository\HistoryRepository;
use App\Repository\PageRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class ReadBookControllerTest extends WebTestCase
{
    /**
     * @test
     */
    public function shouldDisplayAllPagesForOneChapter(): void
    {
        $client = self::createClient();
        /** @var Book $book */
        $book = $client->getContainer()->get(BookRepository::class)->findOneBy(['title' => 'Title fixture 1']);
        /** @var Chapter $chapter */
        $chapter = $client->getContainer()->get(ChapterRepository::class)->findOneBy(['book' => $book]);
        $crawler = $client->request(Request::METHOD_GET, '/book/'.$book->getUuid().'/chapter/'.$chapter->getUuid());

        self::assertResponseIsSuccessful();
        self::assertCount(
            count($client->getContainer()->get(PageRepository::class)->findBy(['chapter' => $chapter])),
            $crawler->filter('div.container')
        );
    }

    /**
     * @test
     */
    public function shouldThrowNotFoundExceptionIfBookIsNotFound(): void
    {
        $client = self::createClient();
        $client->catchExceptions(false);

        self::expectException(NotFoundHttpException::class);
        $client->request(Request::METHOD_GET, '/book/1ed22f9f-8793-6c00-ad9e-1d77bf6a666b/chapter/1ed22f9f-8793-6c00-ad9e-1d77bf6a790b');
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
    public function shouldDisplayAllChapters(): void
    {
        $client = self::createClient();
        /** @var Book $book */
        $book = $client->getContainer()->get(BookRepository::class)->findOneBy(['title' => 'Title fixture 1']);
        /** @var array<array-key, Chapter> $chapters */
        $chapters = $client->getContainer()->get(ChapterRepository::class)->findBy(['book' => $book]);
        $crawler = $client->request(Request::METHOD_GET, '/book/'.$book->getUuid().'/chapter/'.$chapters[0]->getUuid());

        self::assertCount(count($chapters), $crawler->filter('main div ul li'));
    }

    /**
     * @test
     */
    public function shouldAddClassForCurrentChapter(): void
    {
        $client = self::createClient();
        /** @var Book $book */
        $book = $client->getContainer()->get(BookRepository::class)->findOneBy(['title' => 'Title fixture 1']);
        /** @var array<array-key, Chapter> $chapters */
        $chapters = $client->getContainer()->get(ChapterRepository::class)->findBy(['book' => $book]);

        $client->request(Request::METHOD_GET, '/book/'.$book->getUuid().'/chapter/'.$chapters[2]->getUuid());

        self::assertSelectorTextSame('main div ul li a.underline', $chapters[2]->getTitle());
    }

    /**
     * @test
     */
    public function shouldDisplayNextChapterLink(): void
    {
        $client = self::createClient();
        /** @var Book $book */
        $book = $client->getContainer()->get(BookRepository::class)->findOneBy(['title' => 'Title fixture 1']);
        /** @var array<array-key, Chapter> $chapters */
        $chapters = $client->getContainer()->get(ChapterRepository::class)->findBy(['book' => $book]);
        $client->request(Request::METHOD_GET, '/book/'.$book->getUuid().'/chapter/'.$chapters[count($chapters) - 2]->getUuid());

        self::assertResponseIsSuccessful();
        self::assertSelectorTextSame(
            'main div > a[href="/book/'.$book->getUuid().'/chapter/'.$chapters[count($chapters) - 1]->getUuid().'"]',
            'Next chapter'
        );
    }

    /**
     * @test
     */
    public function shouldCreateHistory(): void
    {
        $client = self::createClient();
        /** @var Book $book */
        $book = $client->getContainer()->get(BookRepository::class)->findOneBy(['title' => 'Title fixture 1']);
        /** @var array<array-key, Chapter> $chapters */
        $chapters = $client->getContainer()->get(ChapterRepository::class)->findBy(['book' => $book]);
        /** @var User $user */
        $user = $client->getContainer()->get(UserRepository::class)->findOneBy(['email' => 'user1@email.com']);

        $client->loginUser($user);
        $client->request(Request::METHOD_GET, '/book/'.$book->getUuid().'/chapter/'.$chapters[2]->getUuid());

        /** @var History $history */
        $history = $client->getContainer()->get(HistoryRepository::class)->findOneBy(['chapter' => $chapters[2]]);

        self::assertEquals($user->getUuid(), $history->getUser()->getUuid());
        self::assertEquals($book->getUuid(), $history->getBook()->getUuid());
        self::assertEquals($chapters[2]->getUuid(), $history->getChapter()->getUuid());
    }
}
