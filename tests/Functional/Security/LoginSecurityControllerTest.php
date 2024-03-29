<?php

declare(strict_types=1);

namespace App\Tests\Functional\Security;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Bundle\SecurityBundle\DataCollector\SecurityDataCollector;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Profiler\Profile;

final class LoginSecurityControllerTest extends WebTestCase
{
    /**
     * @test
     */
    public function shouldLogin(): void
    {
        $client = self::createClient();
        $client->request(Request::METHOD_GET, '/login');

        self::assertResponseIsSuccessful();

        $client->enableProfiler();
        $client->submitForm('Login', ['_username' => 'user1@email.com', '_password' => 'password']);

        self::assertResponseStatusCodeSame(Response::HTTP_FOUND);

        if (($profile = $client->getProfile()) instanceof Profile) {
            /** @var SecurityDataCollector $securityCollector */
            $securityCollector = $profile->getCollector('security');

            self::assertTrue($securityCollector->isAuthenticated());
        }
    }

    /**
     * @param array{_username: string, _password: string} $formData
     *
     * @dataProvider provideInvalidData
     *
     * @test
     */
    public function shouldNotLogin(array $formData): void
    {
        $client = self::createClient();
        $client->request(Request::METHOD_GET, '/login');

        self::assertResponseIsSuccessful();

        $client->enableProfiler();
        $client->submitForm('Login', $formData);

        self::assertResponseStatusCodeSame(Response::HTTP_FOUND);

        if (($profile = $client->getProfile()) instanceof Profile) {
            /** @var SecurityDataCollector $securityCollector */
            $securityCollector = $profile->getCollector('security');

            self::assertFalse($securityCollector->isAuthenticated());
        }
    }

    /**
     * @return \Generator<string, array<array-key, array<string, string>>>
     */
    public function provideInvalidData(): iterable
    {
        yield 'wrong email' => [$this->createDataForLogin(['_username' => 'fail@email.com'])];
        yield 'empty email' => [$this->createDataForLogin(['_username' => ''])];
        yield 'wrong password' => [$this->createDataForLogin(['_password' => 'fail'])];
        yield 'empty password' => [$this->createDataForLogin(['_password' => ''])];
        yield 'empty csrf' => [$this->createDataForLogin(['_csrf_token' => ''])];
        yield 'wrong csrf' => [$this->createDataForLogin(['_csrf_token' => 'fail'])];
    }

    /**
     * @param array<string, string> $extraData
     *
     * @return array<string, string>
     */
    private function createDataForLogin(array $extraData): array
    {
        return $extraData + [
                '_username' => 'test@email.com',
                '_password' => 'password',
            ];
    }
}
