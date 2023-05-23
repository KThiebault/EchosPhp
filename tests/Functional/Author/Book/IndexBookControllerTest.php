<?php

declare(strict_types=1);

namespace App\Tests\Functional\Author\Book;

use App\Controller\Author\Book\IndexBookController;
use App\Entity\Book;
use App\Entity\User;
use App\Repository\BookRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class IndexBookControllerTest extends WebTestCase
{
    /**
     * @test
     */
    public function shouldDisplayPageOneUserBooks(): void
    {
        $client = self::createClient();
        /** @var User $user */
        $user = $client->getContainer()->get(UserRepository::class)->findOneBy(['email' => 'user1@email.com']);

        $client->loginUser($user);
        $crawler = $client->request(Request::METHOD_GET, '/author/book');

        self::assertResponseIsSuccessful();
        self::assertCount(IndexBookController::PAGE_SIZE, $crawler->filter('main table tbody tr'));
    }

    /**
     * @test
     */
    public function shouldRedirectToTheLoginPageIfUserIsNotLogin(): void
    {
        $client = self::createClient();
        $client->request(Request::METHOD_GET, '/author/book');

        self::assertResponseRedirects();
        self::assertResponseStatusCodeSame(Response::HTTP_FOUND);

        $client->followRedirect();

        self::assertEquals('/login', $client->getRequest()->getPathInfo());
        self::assertResponseStatusCodeSame(Response::HTTP_OK);
    }
}
