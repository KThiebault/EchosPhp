<?php

declare(strict_types=1);

namespace App\Tests\Functional\Admin\User;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class ShowUserControllerTest extends WebTestCase
{
    /**
     * @test
     */
    public function shouldDisplayOneBookWithThisChapters(): void
    {
        $client = self::createClient();
        /** @var User $user */
        $user = $client->getContainer()->get(UserRepository::class)->findOneBy(['email' => 'admin1@email.com']);
        $client->loginUser($user);

        $client->request(Request::METHOD_GET, '/admin/user/'.$user->getUuid());

        self::assertResponseIsSuccessful();
        self::assertSelectorTextSame('h3', $user->getPseudo());
    }

    /**
     * @test
     */
    public function shouldThrowNotFoundExceptionIfUserIsNotFound(): void
    {
        $client = self::createClient();
        $client->catchExceptions(false);

        /** @var User $user */
        $user = $client->getContainer()->get(UserRepository::class)->findOneBy(['email' => 'admin1@email.com']);
        $client->loginUser($user);

        self::expectException(NotFoundHttpException::class);
        $client->request(Request::METHOD_GET, '/admin/user/1ed22f9f-8793-6c00-ad9e-1d77bf6a790b');
    }

    /**
     * @test
     */
    public function shouldRedirectToTheLoginPageIfUserIsNotLogin(): void
    {
        $client = self::createClient();
        /** @var User $user */
        $user = $client->getContainer()->get(UserRepository::class)->findOneBy(['email' => 'user1@email.com']);
        $client->request(Request::METHOD_GET, '/admin/user/'.$user->getUuid());

        self::assertResponseRedirects();
        self::assertResponseStatusCodeSame(Response::HTTP_FOUND);

        $client->followRedirect();

        self::assertEquals('/login', $client->getRequest()->getPathInfo());
        self::assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    /**
     * @test
     */
    public function shouldRedirectToTheLoginPageIfUserIsNotAdmin(): void
    {
        $client = self::createClient();
        /** @var User $user */
        $user = $client->getContainer()->get(UserRepository::class)->findOneBy(['email' => 'user1@email.com']);

        $client->loginUser($user);
        $client->request(Request::METHOD_GET, '/admin/user/'.$user->getUuid());

        self::assertResponseRedirects();
        self::assertResponseStatusCodeSame(Response::HTTP_FOUND);

        $client->followRedirect();

        self::assertEquals('/book', $client->getRequest()->getPathInfo());
        self::assertResponseStatusCodeSame(Response::HTTP_OK);
    }
}