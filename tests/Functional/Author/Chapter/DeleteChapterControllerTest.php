<?php

declare(strict_types=1);

namespace App\Tests\Functional\Author\Chapter;

use App\Entity\Book;
use App\Entity\Chapter;
use App\Entity\User;
use App\Repository\BookRepository;
use App\Repository\ChapterRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class DeleteChapterControllerTest extends WebTestCase
{
    /**
     * @test
     */
    public function shouldDeleteChapter(): void
    {
        $client = self::createClient();
        /** @var Book $book */
        $book = self::getContainer()->get(BookRepository::class)->findOneBy(['title' => 'Title fixture 1']);
        /** @var array<array-key, Chapter> $chapters */
        $chapters = self::getContainer()->get(ChapterRepository::class)->findBy(['book' => $book]);

        /** @var User $user */
        $user = $client->getContainer()->get(UserRepository::class)->findOneBy(['email' => 'user1@email.com']);
        $client->loginUser($user);
        $client->request(Request::METHOD_GET, '/author/book/'.$book->getUuid().'/chapter');

        $client->submitForm('Delete');
        $crawler = $client->followRedirect();

        self::assertResponseStatusCodeSame(Response::HTTP_OK);
        self::assertCount(count($chapters) - 1, $crawler->filter('main table tbody tr'));
    }

    /**
     * @test
     */
    public function shouldRedirectToTheLoginPageIfUserIsNotLogin(): void
    {
        $client = self::createClient();
        /** @var Book $book */
        $book = self::getContainer()->get(BookRepository::class)->findOneBy(['title' => 'Title fixture 1']);
        /** @var Chapter $chapter */
        $chapter = self::getContainer()->get(ChapterRepository::class)->findOneBy(['book' => $book]);

        $client->request(Request::METHOD_POST, '/author/book/'.$book->getUuid().'/chapter/delete/'.$chapter->getUuid());

        self::assertResponseRedirects();
        self::assertResponseStatusCodeSame(Response::HTTP_FOUND);

        $client->followRedirect();

        self::assertEquals('/login', $client->getRequest()->getPathInfo());
        self::assertResponseStatusCodeSame(Response::HTTP_OK);
    }
}
