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
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class IndexChapterControllerTest extends WebTestCase
{
    /**
     * @test
     */
    public function shouldListAllChaptersForOneBook(): void
    {
        $client = self::createClient();
        /** @var Book $book */
        $book = self::getContainer()->get(BookRepository::class)->findOneBy(['title' => 'Title fixture 1']);
        /** @var array<array-key, Chapter> $chapters */
        $chapters = self::getContainer()->get(ChapterRepository::class)->findBy(['book' => $book->getUuid()]);

        /** @var User $user */
        $user = $client->getContainer()->get(UserRepository::class)->findOneBy(['email' => 'user1@email.com']);
        $client->loginUser($user);
        $crawler = $client->request(Request::METHOD_GET, '/author/book/'.$book->getUuid().'/chapter');

        self::assertResponseIsSuccessful();
        self::assertCount(count($chapters), $crawler->filter('main table tbody tr'));
    }

    /**
     * @test
     */
    public function shouldRedirectToTheLoginPageIfUserIsNotLogin(): void
    {
        $client = self::createClient();
        /** @var Book $book */
        $book = self::getContainer()->get(BookRepository::class)->findOneBy(['title' => 'Title fixture 1']);
        $client->request(Request::METHOD_GET, '/author/book/'.$book->getUuid().'/chapter');

        self::assertResponseRedirects();
        self::assertResponseStatusCodeSame(Response::HTTP_FOUND);

        $client->followRedirect();

        self::assertEquals('/login', $client->getRequest()->getPathInfo());
        self::assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    /**
     * @test
     */
    public function shouldThrowNotFoundExceptionIfBookIsNotFound(): void
    {
        $client = self::createClient();
        /** @var User $user */
        $user = $client->getContainer()->get(UserRepository::class)->findOneBy(['email' => 'user1@email.com']);
        $client->loginUser($user);
        $client->catchExceptions(false);

        self::expectException(NotFoundHttpException::class);
        $client->request(Request::METHOD_GET, 'author/book/1ed22f9f-8793-6c00-ad9e-1d77bf6a790b/chapter');
    }

    /**
     * @test
     */
    public function shouldRedirectWithForbiddenStatusCode(): void
    {
        $client = self::createClient();
        /** @var Book $book */
        $book = self::getContainer()->get(BookRepository::class)->findOneBy(['title' => 'Title fixture 1']);

        /** @var User $user */
        $user = $client->getContainer()->get(UserRepository::class)->findOneBy(['email' => 'user5@email.com']);
        $client->loginUser($user);
        $client->request(Request::METHOD_GET, 'author/book/'.$book->getUuid().'/chapter');

        self::assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }
}
