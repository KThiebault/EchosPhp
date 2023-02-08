<?php

declare(strict_types=1);

namespace App\Tests\Functional\Book;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;

final class IndexBookControllerTest extends WebTestCase
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
    public function shouldNotDisplayHistoryIfUserIsNotLogged(): void
    {
        $client = self::createClient();
        $client->request(Request::METHOD_GET, '/book');

        self::assertSelectorNotExists('main h3', 'Last reading');
    }

    /**
     * @test
     */
    public function shouldNotDisplayHistoryIfUserDoesntHaveHistory(): void
    {
        $client = self::createClient();
        /** @var User $user */
        $user = $client->getContainer()->get(UserRepository::class)->findOneBy(['email' => 'user1@email.com']);

        $client->loginUser($user);
        $client->request(Request::METHOD_GET, '/book');

        self::assertSelectorNotExists('main h3');
    }

    /**
     * @test
     */
    public function shouldNotDisplayHistory(): void
    {
        $client = self::createClient();
        /** @var User $user */
        $user = $client->getContainer()->get(UserRepository::class)->findOneBy(['email' => 'user2@email.com']);

        $client->loginUser($user);
        $client->request(Request::METHOD_GET, '/book');

        self::assertSelectorTextContains('main h3', 'Last reading');
    }
}
