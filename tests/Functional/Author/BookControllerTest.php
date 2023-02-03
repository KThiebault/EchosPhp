<?php

declare(strict_types=1);

namespace App\Tests\Functional\Author;

use App\Entity\Book;
use App\Repository\BookRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class BookControllerTest extends WebTestCase
{
    /**
     * @test
     */
    public function shouldThrowNotFoundExceptionIfBookIsNotFound(): void
    {
        $client = self::createClient();
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
        /** @var Book $book */
        $book = self::getContainer()->get(BookRepository::class)->findOneBy(['title' => 'Title fixture 1']);
        $client->request(Request::METHOD_GET, '/author/book/update/'.$book->getUuid());
        $client->submitForm('Update', $updateBookFormData);

        self::assertResponseStatusCodeSame(Response::HTTP_FOUND);
        self::assertNotSame($updateBookFormData['book[title]'], $book->getTitle());
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
}
