<?php

declare(strict_types=1);

namespace App\Tests\Functional\Author\Chapter;

use App\Entity\Book;
use App\Entity\Chapter;
use App\Entity\User;
use App\Repository\BookRepository;
use App\Repository\ChapterRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Uid\UuidV6;

final class CreateChapterControllerTest extends WebTestCase
{
    /**
     * @param array<string, string> $chapterFormData
     *
     * @dataProvider provideGoodChapterData
     *
     * @test
     */
    public function shouldCreateChapter(array $chapterFormData): void
    {
        $client = self::createClient();
        /** @var Book $book */
        $book = self::getContainer()->get(BookRepository::class)->findOneBy(['title' => 'Title fixture 1']);

        /** @var User $user */
        $user = $client->getContainer()->get(UserRepository::class)->findOneBy(['email' => 'user1@email.com']);
        $client->loginUser($user);
        $client->request(Request::METHOD_GET, '/author/book/'.$book->getUuid().'/chapter/create');

        $client->submitForm('Create', $chapterFormData);

        /** @var Chapter $chapter */
        $chapter = self::getContainer()->get(ChapterRepository::class)->findOneBy([
            'book' => $book->getUuid(),
            'title' => $chapterFormData['chapter[title]'],
        ]);

        self::assertResponseStatusCodeSame(Response::HTTP_FOUND);
        self::assertInstanceOf(UuidV6::class, $chapter->getUuid());
    }

    /**
     * @param array<string, string> $chapterFormData
     *
     * @dataProvider provideBadChapterData
     *
     * @test
     */
    public function shouldNotCreateChapterAndDisplayGoodErrorMessage(array $chapterFormData, string $errorMessage): void
    {
        $client = self::createClient();
        /** @var Book $book */
        $book = self::getContainer()->get(BookRepository::class)->findOneBy(['title' => 'Title fixture 1']);
        $chapterRepository = self::getContainer()->get(ChapterRepository::class);
        $countChapter = count($chapterRepository->findBy(['book' => $book->getUuid()]));

        /** @var User $user */
        $user = $client->getContainer()->get(UserRepository::class)->findOneBy(['email' => 'user1@email.com']);
        $client->loginUser($user);
        $client->request(Request::METHOD_GET, '/author/book/'.$book->getUuid().'/chapter/create');

        $client->submitForm('Create', $chapterFormData);

        self::assertResponseStatusCodeSame(Response::HTTP_UNPROCESSABLE_ENTITY);
        self::assertCount($countChapter, $chapterRepository->findBy(['book' => $book->getUuid()]));
        self::assertSelectorTextSame('main ul > li', $errorMessage);
    }

    public function shouldThrowNotFoundExceptionIfBookIsNotFound(): void
    {
        $client = self::createClient();
        $client->catchExceptions(false);
        /** @var User $user */
        $user = $client->getContainer()->get(UserRepository::class)->findOneBy(['email' => 'user1@email.com']);
        $client->loginUser($user);

        self::expectException(NotFoundHttpException::class);
        $client->request(Request::METHOD_GET, '/author/book/1ed22f9f-8793-6c00-ad9e-1d77bf6a790b/chapter/create');
    }

    /**
     * @test
     */
    public function shouldRedirectToTheLoginPageIfUserIsNotLogin(): void
    {
        $client = self::createClient();
        /** @var Book $book */
        $book = self::getContainer()->get(BookRepository::class)->findOneBy(['title' => 'Title fixture 1']);
        /** @var Chapter $chapter */
        $chapter = self::getContainer()->get(ChapterRepository::class)->findOneBy(['book' => $book]);

        $client->request(Request::METHOD_POST, '/author/book/'.$book->getUuid().'/chapter/create');

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
        $client->request(Request::METHOD_POST, '/author/book/'.$book->getUuid().'/chapter/create');

        self::assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }

    /**
     * @return \Generator<array<array-key, array<string, string>>>
     */
    public function provideGoodChapterData(): \Generator
    {
        yield [
            ['chapter[title]' => 'Chapter 10'],
        ];
    }

    /**
     * @return \Generator<array<array-key, array<string, string>|string>>
     */
    public function provideBadChapterData(): \Generator
    {
        yield [['chapter[title]' => ''], 'This value should not be blank.'];
    }
}
