<?php

declare(strict_types=1);

namespace App\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;

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
        self::assertCount(12, $crawler->filter('div'));
    }
}
