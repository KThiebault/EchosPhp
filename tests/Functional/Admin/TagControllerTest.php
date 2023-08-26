<?php

declare(strict_types=1);

namespace App\Tests\Functional\Admin;

use App\Repository\BookRepository;
use App\Repository\TagRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;

final class TagControllerTest extends WebTestCase
{
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
}
