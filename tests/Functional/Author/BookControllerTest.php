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
     * @param array<string, string> $bookFormData
     * @dataProvider provideGoodBookData
     */
    public function testCreateBookWithGoodData(array $bookFormData): void
    {
        $client = self::createClient();
        $client->request(Request::METHOD_GET, '/book/create');
        $client->submitForm('Create', $bookFormData);

        /** @var Book $book */
        $book = self::getContainer()->get(BookRepository::class)->findOneBy(['title' => $bookFormData['book[title]']]);

        self::assertResponseStatusCodeSame(Response::HTTP_OK);
        self::assertInstanceOf(UuidV6::class, $book->getId());
    }

    /**
     * @param array<string, string> $bookFormData
     * @dataProvider provideBadBookData
     */
    public function testCreateBookWithBadData(array $bookFormData, string $errorMessage): void
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
