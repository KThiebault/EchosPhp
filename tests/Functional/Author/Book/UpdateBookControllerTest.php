<?php

declare(strict_types=1);

namespace App\Tests\Functional\Author\Book;

use App\Entity\Book;
use App\Entity\User;
use App\Repository\BookRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class UpdateBookControllerTest extends WebTestCase
{
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
        $client->request(Request::METHOD_GET, '/author/book/update/1ed22f9f-8793-6c00-ad9e-1d77bf6a790b');
    }

    /**
     * @param array<string, string> $updateBookFormData
     *
     * @dataProvider provideGoodUpdatedBookData
     *
     * @test
     */
    public function shouldUpdateBook(array $updateBookFormData): void
    {
        $client = self::createClient();

        /** @var User $user */
        $user = $client->getContainer()->get(UserRepository::class)->findOneBy(['email' => 'user1@email.com']);
        /** @var Book $book */
        $book = self::getContainer()->get(BookRepository::class)->findOneBy(['author' => $user]);

        $client->loginUser($user);
        $client->request(Request::METHOD_GET, '/author/book/update/'.$book->getUuid());
        $client->submitForm('Update', $updateBookFormData);

        self::assertResponseStatusCodeSame(Response::HTTP_FOUND);
        $client->followRedirect();

        self::assertSelectorTextSame('p.text-gray-500', sprintf('%s has been updated.', $updateBookFormData['book[title]']));
        self::assertNotSame($updateBookFormData['book[title]'], $book->getTitle());
    }

    /**
     * @test
     *
     * @dataProvider provideHttpMethod
     */
    public function shouldRedirectToTheLoginPageIfUserIsNotLogin(string $method): void
    {
        $client = self::createClient();
        /** @var Book $book */
        $book = $client->getContainer()->get(BookRepository::class)->findOneBy(['title' => 'Title fixture 1']);
        $client->request($method, '/author/book/update/'.$book->getUuid());

        self::assertResponseRedirects();
        self::assertResponseStatusCodeSame(Response::HTTP_FOUND);

        $client->followRedirect();

        self::assertEquals('/login', $client->getRequest()->getPathInfo());
        self::assertResponseStatusCodeSame(Response::HTTP_OK);
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
        $client->request(Request::METHOD_GET, '/author/book/update/'.$book->getUuid());

        self::assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }

    /**
     * @return \Generator<array<array-key, array<string, string>>>
     */
    public function provideGoodUpdatedBookData(): \Generator
    {
        yield [
            [
                'book[title]' => 'test',
                'book[summary]' => 'test content with 20 characters minimum.',
            ],
        ];
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
