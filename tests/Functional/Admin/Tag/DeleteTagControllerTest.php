<?php

declare(strict_types=1);

namespace App\Tests\Functional\Admin\Tag;

use App\Entity\Tag;
use App\Entity\User;
use App\Repository\BookRepository;
use App\Repository\TagRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class DeleteTagControllerTest extends WebTestCase
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
     * @test
     */
    public function shouldDeleteTagButNotBook(): void
    {
        $client = self::createClient();
        /** @var User $user */
        $user = $client->getContainer()->get(UserRepository::class)->findOneBy(['email' => 'admin1@email.com']);
        $tags = self::getContainer()->get(TagRepository::class)->findAll();
        $book = $tags[1]->getBooks()[0];


        $client->loginUser($user);
        $client->request(Request::METHOD_GET, '/admin/tag');
        $client->submitForm('Delete');

        $crawler = $client->followRedirect();
        $bookAfterDeletedTag = self::getContainer()->get(BookRepository::class)->find($book->getUuid());

        self::assertCount(count($tags) - 1, $crawler->filter('main a'));
        self::assertEquals($book->getUuid(), $bookAfterDeletedTag->getUuid());
    }

    /**
     * @test
     */
    public function shouldRedirectToTheLoginPageIfUserIsNotLogin(): void
    {
        $client = self::createClient();
        /** @var Tag $tag */
        $tag = $client->getContainer()->get(TagRepository::class)->findOneBy(['name' => 'Tag 1']);
        $client->request(Request::METHOD_POST, '/admin/tag/delete/'.$tag->getUuid());

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
        /** @var Tag $tag */
        $tag = $client->getContainer()->get(TagRepository::class)->findOneBy(['name' => 'Tag 1']);

        $client->loginUser($user);
        $client->request(Request::METHOD_POST, '/admin/tag/delete/'.$tag->getUuid());

        self::assertResponseRedirects();
        self::assertResponseStatusCodeSame(Response::HTTP_FOUND);

        $client->followRedirect();

        self::assertEquals('/book', $client->getRequest()->getPathInfo());
        self::assertResponseStatusCodeSame(Response::HTTP_OK);
    }
}