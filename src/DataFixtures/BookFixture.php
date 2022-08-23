<?php

namespace App\DataFixtures;

use App\Entity\Book;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class BookFixture extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        for ($index = 1; $index <= 10; ++$index) {
            $book = new Book();
            $book->setTitle(sprintf('Title fixture %s', $index));
            $book->setSummary(sprintf('Content fixture %s, with minimum 20 characters', $index));
            $manager->persist($book);
        }

        $manager->flush();
    }
}
