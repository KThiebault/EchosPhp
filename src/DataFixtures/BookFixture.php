<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Book;
use App\Entity\Tag;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

final class BookFixture extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $tags = $manager->getRepository(Tag::class)->findAll();

        for ($index = 1; $index <= 12; ++$index) {
            $book = new Book();
            $book->setTitle(sprintf('Title fixture %d', $index));
            $book->setSummary(sprintf('Content fixture %s, with minimum 20 characters', $index));

            if (0 === $index % 2) {
                $book->addTag($tags[random_int(1, 6)]);
            }

            $book->addTag($tags[random_int(1, 6)]);

            $manager->persist($book);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [TagFixture::class];
    }
}
