<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Chapter;
use App\Entity\Page;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

final class PageFixture extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $chapters = $manager->getRepository(Chapter::class)->findAll();

        foreach ($chapters as $chapter) {
            for ($index = 1; $index <= random_int(1, 20); ++$index) {
                $page = new Page();
                $page->setContent(sprintf('A very long content for page number %d', $index));
                $page->setChapter($chapter);
                $manager->persist($page);
            }
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [ChapterFixture::class];
    }
}
