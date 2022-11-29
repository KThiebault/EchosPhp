<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Book;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

final class BookFixture extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        for ($index = 1; $index <= 12; ++$index) {
            $book = new Book();
            $book->setTitle(sprintf('Title fixture %d', $index));
            $book->setSummary(sprintf('Content fixture %s, with minimum 20 characters', $index));
            $manager->persist($book);
        }

        $manager->flush();
    }
}
