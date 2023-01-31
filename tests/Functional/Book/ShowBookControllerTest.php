<?php

declare(strict_types=1);

namespace App\Tests\Functional\Book;

use App\Entity\Book;
use App\Repository\BookRepository;
use App\Repository\ChapterRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class ShowBookControllerTest extends WebTestCase
{
    /**
     * @test
     */
    public function shouldDisplayOneBookWithThisChapters(): void
    {
        $client = self::createClient();
        /** @var Book $book */
        $book = $client->getContainer()->get(BookRepository::class)->findOneBy(['title' => 'Title fixture 1']);
        $crawler = $client->request(Request::METHOD_GET, '/book/'.$book->getUuid());

        self::assertResponseIsSuccessful();
        self::assertCount(
            count($client->getContainer()->get(ChapterRepository::class)->findBy(['book' => $book])),
            $crawler->filter('article')
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
        $client->request(Request::METHOD_GET, '/book/1ed22f9f-8793-6c00-ad9e-1d77bf6a790b');
    }
}
