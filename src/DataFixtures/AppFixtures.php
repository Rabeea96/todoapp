<?php

namespace App\DataFixtures;

use App\Entity\Todo;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $todo1 = new Todo();
        $todo1->setDescription('My first task');
        $manager->persist($todo1);

        $todo2 = new Todo();
        $todo2->setDescription('My second task');
        $todo2->setIsCompleted(TRUE);
        $manager->persist($todo2);

        $todo3 = new Todo();
        $todo3->setDescription('My third task');
        $manager->persist($todo3);

        $manager->flush();
    }
}
