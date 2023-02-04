<?php

declare(strict_types=1);

namespace App\Tests\Functional\Tag;

use App\Entity\Tag;
use App\Repository\TagRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class ShowTagControllerTest extends WebTestCase
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

    /**
     * @test
     */
    public function shouldThrowNotFoundExceptionIfTagIsNotFound(): void
    {
        $client = self::createClient();
        $client->catchExceptions(false);

        self::expectException(NotFoundHttpException::class);
        $client->request(Request::METHOD_GET, '/tag/1ed22f9f-8793-6c00-ad9e-1d77bf6a790b');
    }
}
