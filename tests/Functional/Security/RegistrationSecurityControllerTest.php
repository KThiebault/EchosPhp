<?php

declare(strict_types=1);

namespace App\Tests\Functional\Security;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class RegistrationSecurityControllerTest extends WebTestCase
{
    /**
     * @test
     */
    public function shouldRegister(): void
    {
        $client = self::createClient();
        $client->request(Request::METHOD_GET, '/registration');

        self::assertResponseIsSuccessful();

        $client->submitForm('Register', $this->createDataForRegistration());
        self::assertResponseStatusCodeSame(Response::HTTP_FOUND);

        $client->followRedirect();
        self::assertRouteSame('app_security_login');
    }

    /**
     * @param array<string, string> $formData
     *
     * @dataProvider provideInvalidDataForRegistration
     *
     * @test
     */
    public function shouldNotRegisterDueToInvalidData(array $formData, string $message): void
    {
        $client = static::createClient();
        $client->request(Request::METHOD_GET, '/registration');
        $client->submitForm('Register', $formData);

        self::assertResponseStatusCodeSame(Response::HTTP_UNPROCESSABLE_ENTITY);
        self::assertSelectorTextSame('main ul li', $message);
    }

    /**
     * @return \Generator<array<array-key, array<string, string>|string>>
     */
    public function provideInvalidDataForRegistration(): \Generator
    {
        yield [
            [
                'registration[email]' => '',
                'registration[pseudo]' => 'test',
                'registration[plainPassword]' => 'test1234',
            ],
            'This value should not be blank.',
        ];
        yield [
            [
                'registration[email]' => 'test',
                'registration[pseudo]' => 'test',
                'registration[plainPassword]' => 'test1234',
            ],
            'This value is not a valid email address.',
        ];
        yield [
            [
                'registration[email]' => 'user1@email.com',
                'registration[pseudo]' => 'test',
                'registration[plainPassword]' => 'test1234',
            ],
            'This value is already used.',
        ];
        yield [
            [
                'registration[email]' => 'test@test.fr',
                'registration[pseudo]' => '',
                'registration[plainPassword]' => 'test1234',
            ],
            'This value should not be blank.',
        ];
        yield [
            [
                'registration[email]' => 'test@test.fr',
                'registration[pseudo]' => 'z',
                'registration[plainPassword]' => 'test1234',
            ],
            'This value is too short. It should have 3 characters or more.',
        ];
        yield [
            [
                'registration[email]' => 'user1@email.com',
                'registration[pseudo]' => 'user1',
                'registration[plainPassword]' => 'test1234',
            ],
            'This value is already used.',
        ];
        yield [
            [
                'registration[email]' => 'test@test.fr',
                'registration[pseudo]' => 'test',
                'registration[plainPassword]' => 'test',
            ],
            'This value is too short. It should have 8 characters or more.',
        ];
    }

    /**
     * @param array<string, string> $extraData
     *
     * @return array<string, string>
     */
    private function createDataForRegistration(array $extraData = []): array
    {
        return $extraData + [
                'registration[email]' => 'test11@email.com',
                'registration[plainPassword]' => 'password',
                'registration[pseudo]' => 'test',
            ];
    }
}
