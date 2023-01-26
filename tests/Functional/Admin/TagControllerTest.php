<?php

declare(strict_types=1);

namespace App\Tests\Functional\Admin;

use App\Entity\Tag;
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
            $crawler->filter('main li a')
        );
    }

    /**
     * @test
     */
    public function shouldThrowNotFoundExceptionIfTagIsNotFound(): void
    {
        $client = self::createClient();
        $client->catchExceptions(false);

        self::expectException(NotFoundHttpException::class);
        $client->request(Request::METHOD_GET, '/admin/tag/update/1ed22f9f-8793-6c00-ad9e-1d77bf6a790b');
    }

    /**
     * @param array<string, string> $updateTagFormData
     * @dataProvider provideGoodTagData
     * @test
     */
    public function shouldUpdateTag(array $updateTagFormData): void
    {
        $client = self::createClient();
        /** @var Tag $tag */
        $tag = self::getContainer()->get(TagRepository::class)->findOneBy(['name' => 'Tag 2']);
        $client->request(Request::METHOD_GET, '/admin/tag/update/'.$tag->getUuid());
        $client->submitForm('Update', $updateTagFormData);

        self::assertResponseStatusCodeSame(Response::HTTP_FOUND);
        self::assertNotSame($updateTagFormData['tag[name]'], $tag->getName());
    }

    /**
     * @param array<string, string> $tagFormData
     * @dataProvider provideBadTagData
     * @test
     */
    public function shouldNotUpdateTagAndDisplayErrorMessage(array $tagFormData, string $errorMessage): void
    {
        $client = self::createClient();
        /** @var Tag $tag */
        $tag = self::getContainer()->get(TagRepository::class)->findOneBy(['name' => 'Tag 2']);

        $client->request(Request::METHOD_GET, '/admin/tag/update/'.$tag->getUuid());
        $client->submitForm('Update', $tagFormData);

        self::assertResponseStatusCodeSame(Response::HTTP_UNPROCESSABLE_ENTITY);
        self::assertSelectorTextSame('main ul > li', $errorMessage);
    }

    /**
     * @param array<string, string> $tagFormData
     * @dataProvider provideGoodTagData
     * @test
     */
    public function shouldCreateTag(array $tagFormData): void
    {
        $client = self::createClient();
        $client->request(Request::METHOD_GET, '/admin/tag/create');
        $client->submitForm('Create', $tagFormData);

        /** @var Tag $tag */
        $tag = self::getContainer()->get(TagRepository::class)->findOneBy(['name' => $tagFormData['tag[name]']]);

        self::assertResponseStatusCodeSame(Response::HTTP_FOUND);
        self::assertInstanceOf(UuidV6::class, $tag->getUuid());
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
            'This value should not be blank.'
        ];
        yield [
            ['tag[name]' => 'test'],
            'This value is too short. It should have 5 characters or more.'
        ];
    }
}
