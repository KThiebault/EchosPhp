<?php

declare(strict_types=1);

namespace App\Tests\Functional\Author;

use App\Entity\Book;
use App\Repository\BookRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Uid\UuidV6;

final class BookControllerTest extends WebTestCase
{
    /**
     * @test
     */
    public function shouldDisplayAllBooks(): void
    {
        $client = self::createClient();
        $crawler = $client->request(Request::METHOD_GET, '/book');

        self::assertResponseIsSuccessful();
        self::assertCount(10, $crawler->filter('a'));
    }

    /**
     * @test
     */
    public function shouldDisplayOneBook(): void
    {
        $client = self::createClient();
        /** @var Book $book */
        $book = self::getContainer()->get(BookRepository::class)->findOneBy(['title' => 'Title fixture 1']);
        $crawler = $client->request(Request::METHOD_GET, '/book/update/'.$book->getUuid());

        self::assertResponseIsSuccessful();
        self::assertCount(1, $crawler->filter('div'));
        self::assertSelectorTextSame('div', $book->getTitle());
    }

    /**
     * @param array<string, string> $bookFormData
     * @dataProvider provideGoodBookData
     * @test
     */
    public function shouldCreateBook(array $bookFormData): void
    {
        $client = self::createClient();
        $client->request(Request::METHOD_GET, '/book/create');
        $client->submitForm('Create', $bookFormData);

        /** @var Book $book */
        $book = self::getContainer()->get(BookRepository::class)->findOneBy(['title' => $bookFormData['book[title]']]);

        self::assertResponseStatusCodeSame(Response::HTTP_OK);
        self::assertInstanceOf(UuidV6::class, $book->getUuid());
    }

    /**
     * @param array<string, string> $bookFormData
     * @dataProvider provideBadBookData
     * @test
     */
    public function shouldNotCreateBookAndDisplayGoodErrorMessage(array $bookFormData, string $errorMessage): void
    {
        $client = self::createClient();
        $client->request(Request::METHOD_GET, '/book/create');
        $client->submitForm('Create', $bookFormData);

        self::assertResponseStatusCodeSame(Response::HTTP_OK);
        self::assertSelectorTextSame('ul > li', $errorMessage);
    }

    /**
     * @return \Generator<array<array-key, array<string, string>>>
     */
    private function provideGoodBookData(): \Generator
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
    private function provideBadBookData(): \Generator
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
}
