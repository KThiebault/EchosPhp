<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class UserFixture extends Fixture
{
    public function __construct(private readonly UserPasswordHasherInterface $userPasswordHasher)
    {
    }

    public function load(ObjectManager $manager): void
    {
        for ($index = 1; $index <= 10; ++$index) {
            $user = new User();
            $user->setEmail(sprintf('user%d@email.com', $index));
            $user->setPseudo(sprintf('user%d', $index));
            $user->setRoles([User::ROLE_USER]);
            $user->setPassword($this->userPasswordHasher->hashPassword($user, 'password'));

            $manager->persist($user);
        }

        for ($index = 1; $index <= 2; ++$index) {
            $user = new User();
            $user->setEmail(sprintf('admin%d@email.com', $index));
            $user->setPseudo(sprintf('admin%d', $index));
            $user->setRoles([User::ROLE_ADMIN]);
            $user->setPassword($this->userPasswordHasher->hashPassword($user, 'password'));

            $manager->persist($user);
        }

        $user = new User();
        $user->setEmail('super@email.com');
        $user->setPseudo('super');
        $user->setRoles([User::ROLE_SUPER_ADMIN]);
        $user->setPassword($this->userPasswordHasher->hashPassword($user, 'password'));

        $manager->persist($user);
        $manager->flush();
    }
}
