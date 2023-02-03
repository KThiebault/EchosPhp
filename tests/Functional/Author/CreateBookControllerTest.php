<?php

declare(strict_types=1);

namespace App\Tests\Functional\Author;

use App\Entity\Book;
use App\Entity\User;
use App\Repository\BookRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Uid\UuidV6;

final class CreateBookControllerTest extends WebTestCase
{
    /**
     * @param array<string, string> $bookFormData
     *
     * @dataProvider provideGoodBookData
     *
     * @test
     */
    public function shouldCreateBook(array $bookFormData): void
    {
        $client = self::createClient();
        /** @var User $user */
        $user = $client->getContainer()->get(UserRepository::class)->findOneBy(['email' => 'user1@email.com']);
        $client->loginUser($user);
        $client->request(Request::METHOD_GET, '/author/book/create');
        $client->submitForm('Create', $bookFormData);

        /** @var Book $book */
        $book = self::getContainer()->get(BookRepository::class)->findOneBy(['title' => $bookFormData['book[title]']]);

        self::assertResponseStatusCodeSame(Response::HTTP_FOUND);
        self::assertInstanceOf(UuidV6::class, $book->getUuid());
    }

    /**
     * @param array<string, string> $bookFormData
     *
     * @dataProvider provideBadBookData
     *
     * @test
     */
    public function shouldNotCreateBookAndDisplayErrorMessage(array $bookFormData, string $errorMessage): void
    {
        $client = self::createClient();
        $bookRepository = self::getContainer()->get(BookRepository::class);
        $countBook = count($bookRepository->findAll());

        /** @var User $user */
        $user = $client->getContainer()->get(UserRepository::class)->findOneBy(['email' => 'user1@email.com']);
        $client->loginUser($user);
        $client->request(Request::METHOD_GET, '/author/book/create');
        $client->submitForm('Create', $bookFormData);

        self::assertCount($countBook, $bookRepository->findAll());
        self::assertResponseStatusCodeSame(Response::HTTP_UNPROCESSABLE_ENTITY);
        self::assertSelectorTextSame('main form ul > li', $errorMessage);
    }

    /**
     * @test
     *
     * @dataProvider provideHttpMethod
     */
    public function shouldRedirectToTheLoginPageIfUserIsNotLogin(string $method): void
    {
        $client = self::createClient();
        $client->request($method, '/author/book/create');

        self::assertResponseRedirects();
        self::assertResponseStatusCodeSame(Response::HTTP_FOUND);

        $client->followRedirect();

        self::assertEquals('/login', $client->getRequest()->getPathInfo());
        self::assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    /**
     * @return \Generator<array<array-key, array<string, string>>>
     */
    public function provideGoodBookData(): \Generator
    {
        yield [
            [
                'book[title]' => 'test',
                'book[summary]' => 'test content with 20 characters minimum.',
            ],
        ];
    }

    /**
     * @return \Generator<array<array-key, array<string, string>|string>>
     */
    public function provideBadBookData(): \Generator
    {
        yield [
            [
                'book[title]' => '',
                'book[summary]' => 'test content with 20 characters minimum.',
            ],
            'This value should not be blank.',
        ];
        yield [
            [
                'book[title]' => 'test',
                'book[summary]' => 'test content.',
            ],
            'This value is too short. It should have 20 characters or more.',
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
