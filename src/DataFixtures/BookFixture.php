<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Book;
use App\Entity\Tag;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

final class BookFixture extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $users = $manager->getRepository(User::class)->findAll();
        $tags = $manager->getRepository(Tag::class)->findAll();

        for ($index = 1; $index <= 500; ++$index) {
            $book = new Book();
            $book->setTitle(sprintf('Title fixture %d', $index));
            $book->setSummary(sprintf('Content fixture %s, with minimum 20 characters', $index));

            if (0 === $index % 2) {
                $book->addTag($tags[random_int(1, 6)]);
            }

            $book->addTag($tags[random_int(1, 6)]);
            // @phpstan-ignore-next-line
            $book->setAuthor($users[random_int(0, count($users) - 1)]);

            $manager->persist($book);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [TagFixture::class, UserFixture::class];
    }
}
