<?php

declare(strict_types=1);

namespace App\Tests\Functional\Admin\User;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class SearchUserControllerTest extends WebTestCase
{
    /**
     * @test
     */
    public function shouldFindUser(): void
    {
        $client = self::createClient();
        /** @var User $adminUser */
        $adminUser = $client->getContainer()->get(UserRepository::class)->findOneBy(['email' => 'admin1@email.com']);
        /** @var User $user */
        $user = $client->getContainer()->get(UserRepository::class)->findOneBy(['email' => 'user1@email.com']);

        $client->loginUser($adminUser);
        $client->request(Request::METHOD_GET, '/admin/user/search');

        $client->submitForm('Search', ['search_user[pseudo]' => $user->getPseudo()]);

        self::assertResponseRedirects();
        self::assertResponseStatusCodeSame(Response::HTTP_FOUND);

        $client->followRedirect();

        self::assertEquals('/admin/user/'.$user->getUuid(), $client->getRequest()->getPathInfo());
        self::assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    /**
     * @test
     */
    public function shouldNotFindUser(): void
    {
        $client = self::createClient();
        /** @var User $adminUser */
        $adminUser = $client->getContainer()->get(UserRepository::class)->findOneBy(['email' => 'admin1@email.com']);

        $client->loginUser($adminUser);
        $client->request(Request::METHOD_GET, '/admin/user/search');

        $client->submitForm('Search', ['search_user[pseudo]' => 'test']);

        self::assertResponseStatusCodeSame(Response::HTTP_UNPROCESSABLE_ENTITY);
        self::assertSelectorTextSame('main form ul > li', 'User not found');
    }

    /**
     * @test
     *
     * @dataProvider provideHttpMethod
     */
    public function shouldRedirectToTheLoginPageIfUserIsNotLogin(string $method): void
    {
        $client = self::createClient();
        $client->request($method, '/admin/user/search');

        self::assertResponseRedirects();
        self::assertResponseStatusCodeSame(Response::HTTP_FOUND);

        $client->followRedirect();

        self::assertEquals('/login', $client->getRequest()->getPathInfo());
        self::assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    /**
     * @test
     *
     * @dataProvider provideHttpMethod
     */
    public function shouldRedirectToTheLoginPageIfUserIsNotAdmin(string $method): void
    {
        $client = self::createClient();
        /** @var User $user */
        $user = $client->getContainer()->get(UserRepository::class)->findOneBy(['email' => 'user1@email.com']);

        $client->loginUser($user);
        $client->request($method, '/admin/user/search');

        self::assertResponseRedirects();
        self::assertResponseStatusCodeSame(Response::HTTP_FOUND);

        $client->followRedirect();

        self::assertEquals('/book', $client->getRequest()->getPathInfo());
        self::assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    /**
     * @return \Generator<array<array-key, string>>
     */
    public function provideHttpMethod(): \Generator
    {
        yield [Request::METHOD_GET];
        yield [Request::METHOD_POST];
    }
}