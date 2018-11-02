<?php

namespace AppBundle\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class CoreFixtures extends Fixture
{

    /**
     * @var ObjectManager
     */
    private $manager;

    public function load(ObjectManager $manager): void
    {
        $this->manager = $manager;
    }

    protected function getManager(): ObjectManager
    {
        return $this->manager;
    }
}