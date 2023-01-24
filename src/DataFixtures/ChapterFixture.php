<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Book;
use App\Entity\Chapter;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

final class ChapterFixture extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $books = $manager->getRepository(Book::class)->findAll();

        foreach ($books as $book) {
            for ($index = 1; $index <= random_int(1, 15); ++$index) {
                $chapter = new Chapter();
                $chapter->setTitle(sprintf('Chapter %d', $index));
                $chapter->setBook($book);
                $manager->persist($chapter);
            }
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [BookFixture::class];
    }
}
