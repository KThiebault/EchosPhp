<?php

declare(strict_types=1);

namespace App\Tests\Functional\Author;

use App\Entity\Book;
use App\Repository\BookRepository;
use App\Repository\ChapterRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;

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
}
