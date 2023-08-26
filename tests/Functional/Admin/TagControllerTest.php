<?php

declare(strict_types=1);

namespace App\Tests\Functional\Admin;

use App\Entity\Tag;
use App\Repository\BookRepository;
use App\Repository\TagRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Uid\UuidV6;

final class TagControllerTest extends WebTestCase
{
    /**
     * @test
     */
    public function shouldDisplayAllTags(): void
    {
        $client = self::createClient();
        $crawler = $client->request(Request::METHOD_GET, 'admin/tag');

        self::assertResponseIsSuccessful();
        self::assertCount(
            count($client->getContainer()->get(TagRepository::class)->findAll()),
            $crawler->filter('main a')
        );
    }

    /**
     * @test
     */
    public function shouldDeleteTagButNotBook(): void
    {
        $client = self::createClient();
        $tags = self::getContainer()->get(TagRepository::class)->findAll();
        $book = $tags[1]->getBooks()[0];
        $client->request(Request::METHOD_GET, '/admin/tag');
        $client->submitForm('Delete');

        $crawler = $client->followRedirect();
        $bookAfterDeletedTag = self::getContainer()->get(BookRepository::class)->find($book->getUuid());

        self::assertCount(count($tags) - 1, $crawler->filter('main a'));
        self::assertEquals($book->getUuid(), $bookAfterDeletedTag->getUuid());
    }

    /**
     * @return \Generator<array<array-key, array<string, string>>>
     */
    public function provideGoodTagData(): \Generator
    {
        yield [['tag[name]' => 'test 10']];
    }

    /**
     * @return \Generator<array<array-key, array<string, string>|string>>
     */
    public function provideBadTagData(): \Generator
    {
        yield [
            ['tag[name]' => ''],
            'This value should not be blank.',
        ];
        yield [
            ['tag[name]' => 'test'],
            'This value is too short. It should have 5 characters or more.',
        ];
    }
}
