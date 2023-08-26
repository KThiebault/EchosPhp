<?php

declare(strict_types=1);

namespace App\Tests\Functional\Admin\Tag;

use App\Entity\User;
use App\Repository\TagRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class IndexTagControllerTest extends WebTestCase
{
    /**
     * @test
     */
    public function shouldDisplayAllTags(): void
    {
        $client = self::createClient();
        /** @var User $user */
        $user = $client->getContainer()->get(UserRepository::class)->findOneBy(['email' => 'admin1@email.com']);

        $client->loginUser($user);
        $crawler = $client->request(Request::METHOD_GET, 'admin/tag');

        self::assertResponseIsSuccessful();
        self::assertCount(
            count($client->getContainer()->get(TagRepository::class)->findAll()),
            $crawler->filter('main a')
        );
    }

    /**
     * @test
     *
     * @dataProvider provideHttpMethod
     */
    public function shouldRedirectToTheLoginPageIfUserIsNotLogin(string $method): void
    {
        $client = self::createClient();
        $client->request($method, '/admin/tag');

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
        $client->request($method, '/admin/tag');

        self::assertResponseRedirects();
        self::assertResponseStatusCodeSame(Response::HTTP_FOUND);

        $client->followRedirect();

        self::assertEquals('/book', $client->getRequest()->getPathInfo());
        self::assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    /**
     * @return \Generator<array<array-key, array<string, string>>>
     */
    public function provideGoodTagData(): \Generator
    {
        yield [['tag[name]' => 'test 10']];
    }

    /**
     * @return \Generator<array<array-key, string>>
     */
    public function provideHttpMethod(): \Generator
    {
        yield [Request::METHOD_GET];
    }

    /**
     * @return \Generator<array<array-key, array<string, string|array<array-key, string>>|string>>
     */
    public function provideBadTagData(): \Generator
    {
        yield [['tag[name]' => 'Tag 1'], 'This value is already used.'];
        yield [['tag[name]' => ''], 'This value should not be blank.'];
        yield [['tag[name]' => 'tag'], 'This value is too short. It should have 5 characters or more.'];
    }
}