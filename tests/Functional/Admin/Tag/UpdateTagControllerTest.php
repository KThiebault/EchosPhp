<?php

declare(strict_types=1);

namespace App\Tests\Functional\Admin\Tag;

use App\Entity\Tag;
use App\Entity\User;
use App\Repository\TagRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class UpdateTagControllerTest extends WebTestCase
{
    /**
     * @test
     */
    public function shouldThrowNotFoundExceptionIfTagIsNotFound(): void
    {
        $client = self::createClient();
        /** @var User $user */
        $user = $client->getContainer()->get(UserRepository::class)->findOneBy(['email' => 'admin1@email.com']);
        $client->loginUser($user);

        $client->catchExceptions(false);

        self::expectException(NotFoundHttpException::class);
        $client->request(Request::METHOD_GET, '/admin/tag/update/1ed22f9f-8793-6c00-ad9e-1d77bf6a790b');
    }

    /**
     * @param array<string, string> $updateTagFormData
     *
     * @dataProvider provideGoodTagData
     *
     * @test
     */
    public function shouldUpdateTag(array $updateTagFormData): void
    {
        $client = self::createClient();
        /** @var User $user */
        $user = $client->getContainer()->get(UserRepository::class)->findOneBy(['email' => 'admin1@email.com']);
        /** @var Tag $tag */
        $tag = self::getContainer()->get(TagRepository::class)->findOneBy(['name' => 'Tag 2']);

        $client->loginUser($user);
        $client->request(Request::METHOD_GET, '/admin/tag/update/'.$tag->getUuid());
        $client->submitForm('Update', $updateTagFormData);

        self::assertResponseStatusCodeSame(Response::HTTP_FOUND);
        self::assertNotSame($updateTagFormData['tag[name]'], $tag->getName());
    }

    /**
     * @param array<string, string> $tagFormData
     *
     * @dataProvider provideBadTagData
     *
     * @test
     */
    public function shouldNotUpdateTagAndDisplayErrorMessage(array $tagFormData, string $errorMessage): void
    {
        $client = self::createClient();
        /** @var User $user */
        $user = $client->getContainer()->get(UserRepository::class)->findOneBy(['email' => 'admin1@email.com']);
        /** @var Tag $tag */
        $tag = self::getContainer()->get(TagRepository::class)->findOneBy(['name' => 'Tag 2']);

        $client->loginUser($user);
        $client->request(Request::METHOD_GET, '/admin/tag/update/'.$tag->getUuid());
        $client->submitForm('Update', $tagFormData);

        self::assertResponseStatusCodeSame(Response::HTTP_UNPROCESSABLE_ENTITY);
        self::assertSelectorTextSame('main ul > li', $errorMessage);
    }

    /**
     * @test
     *
     * @dataProvider provideHttpMethod
     */
    public function shouldRedirectToTheLoginPageIfUserIsNotLogin(string $method): void
    {
        $client = self::createClient();
        /** @var Tag $tag */
        $tag = $client->getContainer()->get(TagRepository::class)->findOneBy(['name' => 'Tag 1']);
        $client->request($method, '/admin/tag/update/'.$tag->getUuid());

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
        /** @var Tag $tag */
        $tag = $client->getContainer()->get(TagRepository::class)->findOneBy(['name' => 'Tag 1']);

        $client->loginUser($user);
        $client->request($method, '/admin/tag/update/'.$tag->getUuid());

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
        yield [Request::METHOD_POST];
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