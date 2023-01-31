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
                $page->setContent('Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Sit amet facilisis magna etiam. Maecenas ultricies mi eget mauris pharetra et ultrices. Elementum facilisis leo vel fringilla est ullamcorper eget nulla facilisi. Sed risus pretium quam vulputate dignissim suspendisse in est. Ultricies mi quis hendrerit dolor. In nisl nisi scelerisque eu ultrices. Amet est placerat in egestas erat imperdiet. Vitae elementum curabitur vitae nunc sed. Ut sem nulla pharetra diam sit amet nisl suscipit adipiscing. Mattis enim ut tellus elementum. Convallis a cras semper auctor neque vitae. Consectetur purus ut faucibus pulvinar elementum integer enim neque volutpat. Cras sed felis eget velit aliquet sagittis. Purus sit amet luctus venenatis lectus magna fringilla urna. Adipiscing bibendum est ultricies integer quis auctor. Blandit aliquam etiam erat velit scelerisque in dictum non consectetur.<br><br>
Adipiscing diam donec adipiscing tristique risus nec feugiat. Praesent tristique magna sit amet purus gravida. Nisi est sit amet facilisis magna etiam tempor orci eu. Eget dolor morbi non arcu. Auctor eu augue ut lectus arcu. Consectetur libero id faucibus nisl tincidunt eget nullam. Pellentesque diam volutpat commodo sed. Parturient montes nascetur ridiculus mus mauris. Enim nec dui nunc mattis enim ut tellus elementum sagittis. Leo integer malesuada nunc vel risus. Auctor augue mauris augue neque gravida. Cursus euismod quis viverra nibh. Nunc lobortis mattis aliquam faucibus purus. Congue quisque egestas diam in arcu cursus euismod. Arcu felis bibendum ut tristique et egestas. Sit amet consectetur adipiscing elit duis tristique sollicitudin nibh. Mauris cursus mattis molestie a. Fringilla phasellus faucibus scelerisque eleifend donec pretium vulputate sapien.<br><br>
Ac tortor dignissim convallis aenean et tortor at. Odio ut sem nulla pharetra diam sit amet nisl suscipit. Faucibus interdum posuere lorem ipsum dolor sit amet consectetur adipiscing. Adipiscing elit duis tristique sollicitudin nibh sit amet. Quis risus sed vulputate odio ut enim blandit volutpat maecenas. Diam in arcu cursus euismod quis viverra nibh. Euismod quis viverra nibh cras pulvinar mattis nunc sed. Pharetra vel turpis nunc eget. Sed augue lacus viverra vitae congue. Pulvinar sapien et ligula ullamcorper malesuada proin libero. Id aliquet lectus proin nibh nisl. Est ante in nibh mauris cursus mattis. Pellentesque habitant morbi tristique senectus et netus et malesuada. Integer enim neque volutpat ac tincidunt vitae semper quis. Pharetra et ultrices neque ornare aenean euismod. In eu mi bibendum neque egestas congue quisque egestas diam. Neque convallis a cras semper auctor neque.<br><br>
Volutpat odio facilisis mauris sit amet massa vitae tortor.<br> Ante metus dictum at tempor commodo ullamcorper. Pulvinar mattis nunc sed blandit libero volutpat. Cras ornare arcu dui vivamus arcu felis bibendum. Ultrices sagittis orci a scelerisque purus. Quis varius quam quisque id diam vel. Parturient montes nascetur ridiculus mus mauris vitae ultricies. Consequat mauris nunc congue nisi vitae suscipit tellus mauris. Eget duis at tellus at urna condimentum. Blandit turpis cursus in hac habitasse platea dictumst quisque sagittis. Felis donec et odio pellentesque diam. Auctor elit sed vulputate mi sit.<br><br>
Vestibulum sed arcu non odio euismod lacinia at quis risus. Commodo odio aenean sed adipiscing diam donec adipiscing. Gravida neque convallis a cras semper auctor. Donec enim diam vulputate ut pharetra. Imperdiet nulla malesuada pellentesque elit eget gravida cum sociis. Tellus id interdum velit laoreet id. Placerat vestibulum lectus mauris ultrices eros. Ultricies tristique nulla aliquet enim tortor at auctor urna nunc. Egestas quis ipsum suspendisse ultrices gravida dictum. Duis at consectetur lorem donec massa sapien faucibus. Viverra accumsan in nisl nisi scelerisque eu ultrices vitae. Ullamcorper velit sed ullamcorper morbi. A diam sollicitudin tempor id eu nisl nunc mi ipsum. Scelerisque eleifend donec pretium vulputate sapien nec sagittis aliquam malesuada.
Ac tortor dignissim convallis aenean et tortor at. Odio ut sem nulla pharetra diam sit amet nisl suscipit. Faucibus interdum posuere lorem ipsum dolor sit amet consectetur adipiscing. Adipiscing elit duis tristique sollicitudin nibh sit amet. Quis risus sed vulputate odio ut enim blandit volutpat maecenas. Diam in arcu cursus euismod quis viverra nibh. Euismod quis viverra nibh cras pulvinar mattis nunc sed. Pharetra vel turpis nunc eget. Sed augue lacus viverra vitae congue. Pulvinar sapien et ligula ullamcorper malesuada proin libero. Id aliquet lectus proin nibh nisl. Est ante in nibh mauris cursus mattis. Pellentesque habitant morbi tristique senectus et netus et malesuada. Integer enim neque volutpat ac tincidunt vitae semper quis. Pharetra et ultrices neque ornare aenean euismod. In eu mi bibendum neque egestas congue quisque egestas diam. Neque convallis a cras semper auctor neque.<br><br>
                ');
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
