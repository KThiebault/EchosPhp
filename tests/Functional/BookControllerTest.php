<?php

declare(strict_types=1);

namespace App\Tests\Functional;

use App\Entity\Book;
use App\Repository\BookRepository;
use App\Repository\ChapterRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;

final class BookControllerTest extends WebTestCase
{
    /**
     * @test
     */
    public function shouldDisplayTwelveBooks(): void
    {
        $client = self::createClient();
        $crawler = $client->request(Request::METHOD_GET, '/book');

        self::assertResponseIsSuccessful();
        self::assertCount(12, $crawler->filter('a'));
    }

    /**
     * @test
     */
    public function shouldDisplayOneBooksWithThisChapters(): void
    {
        $client = self::createClient();
        /** @var Book $book */
        $book = $client->getContainer()->get(BookRepository::class)->findOneBy(['title' => 'Title fixture 1']);
        $chapterCount = count($client->getContainer()->get(ChapterRepository::class)->findBy(['book' => $book]));

        $crawler = $client->request(Request::METHOD_GET, '/book/'.$book->getUuid());

        self::assertResponseIsSuccessful();
        self::assertCount($chapterCount, $crawler->filter('a'));
    }
}
