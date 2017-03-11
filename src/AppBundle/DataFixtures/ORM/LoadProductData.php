<?php

namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Entity\Product;

class LoadUserData implements FixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $products = [
            [
                'Apple',
                'iPhone 6',
                '32GB',
                'silver',
                'Apple iPhone 6 32GB silver',
                '489',
                '16'
            ],
            [
                'Apple',
                'iPhone 6 Plus',
                '64GB',
                'gold',
                'Apple iPhone 6 Plus 64GB gold',
                '559',
                '9'
            ],
            [
                'Apple',
                'iPhone 7',
                '128GB',
                'black',
                'Apple iPhone 7 128GB black',
                '799',
                '21'
            ],
        ];

        foreach($products as list($a, $b, $c, $d, $e, $f, $g)){
            $var = new Product();
            $var->setBrand($a);
            $var->setModel($b);
            $var->setCapacity($c);
            $var->setColor($d);
            $var->setDescription($e);
            $var->setPrice($f);
            $var->setQuantity($g);

            $manager->persist($var);
            $manager->flush();
        }
    }
}