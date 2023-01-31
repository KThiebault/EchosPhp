<?php

namespace App\DataFixtures;

use App\Entity\Tag;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class TagFixture extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        for ($index = 1; $index <= 7; ++$index) {
            $tag = new Tag();
            $tag->setName(sprintf('Tag %d', $index));

            $manager->persist($tag);
        }

        $manager->flush();
    }
}
