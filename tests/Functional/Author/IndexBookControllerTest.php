<?php

declare(strict_types=1);

namespace App\Tests\Functional\Author;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class IndexBookControllerTest extends WebTestCase
{
    /**
     * @test
     */
    public function shouldDisplayAllBooks(): void
    {
        $client = self::createClient();
        $client->loginUser($client->getContainer()->get(UserRepository::class)->findOneBy(['email' => 'user1@email.com']));
        $crawler = $client->request(Request::METHOD_GET, '/author/book');

        self::assertResponseIsSuccessful();
        self::assertCount(12, $crawler->filter('main a'));
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
