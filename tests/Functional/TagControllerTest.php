<?php

declare(strict_types=1);

namespace App\Tests\Functional;

use App\Entity\Tag;
use App\Repository\TagRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;

final class TagControllerTest extends WebTestCase
{
    /**
     * @test
     */
    public function shouldDisplayAllBooksForOneTag(): void
    {
        $client = self::createClient();
        /** @var Tag $tag */
        $tag = $client->getContainer()->get(TagRepository::class)->findOneBy(['name' => 'Tag 2']);
        $crawler = $client->request(Request::METHOD_GET, '/tag/'.$tag->getUuid());

        self::assertResponseIsSuccessful();
        self::assertSelectorTextSame('h2', $tag->getName());
        self::assertCount($tag->getBooks()->count(), $crawler->filter('main article'));
    }
}
