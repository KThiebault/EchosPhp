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

final class DeleteBookControllerTest extends WebTestCase
{
    /**
     * @test
     */
    public function shouldDeleteBook(): void
    {
        $client = self::createClient();
        /** @var User $user */
        $user = $client->getContainer()->get(UserRepository::class)->findOneBy(['email' => 'user1@email.com']);
        $countBook = count(self::getContainer()->get(BookRepository::class)->findBy(['author' => $user]));

        $client->loginUser($user);
        $client->request(Request::METHOD_GET, '/author/book');

        $client->submitForm('Delete');
        $client->followRedirect();

        self::assertSelectorTextSame('main table tfoot span:nth-child(3)', (string)($countBook - 1));
    }

    /**
     * @test
     */
    public function shouldRedirectToTheLoginPageIfUserIsNotLogin(): void
    {
        $client = self::createClient();
        /** @var Book $book */
        $book = $client->getContainer()->get(BookRepository::class)->findOneBy(['title' => 'Title fixture 1']);

        $client->request(Request::METHOD_POST, '/author/book/delete/'.$book->getUuid());

        self::assertResponseRedirects();
        self::assertResponseStatusCodeSame(Response::HTTP_FOUND);

        $client->followRedirect();

        self::assertEquals('/login', $client->getRequest()->getPathInfo());
        self::assertResponseStatusCodeSame(Response::HTTP_OK);
    }
}
