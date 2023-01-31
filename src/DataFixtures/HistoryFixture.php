<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Book;
use App\Entity\Chapter;
use App\Entity\History;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

final class HistoryFixture extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        /** @var User $user */
        $user = $manager->getRepository(User::class)->findOneBy(['email' => 'user2@email.com']);
        /** @var Book $book */
        $book = $manager->getRepository(Book::class)->findOneBy(['title' => 'Title fixture 1']);
        /** @var Chapter $chapter */
        $chapter = $manager->getRepository(Chapter::class)->findOneBy(['book' => $book]);

        $history = new History();
        $history->setUser($user);
        $history->setBook($book);
        $history->setChapter($chapter);

        $manager->persist($history);
        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            UserFixture::class,
            BookFixture::class,
            ChapterFixture::class,
        ];
    }
}
