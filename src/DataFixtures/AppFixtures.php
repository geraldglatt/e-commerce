<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Product;
use Liior\Faker\Prices;
use App\Entity\Category;
use bluemmb\Faker\PicsumPhotosProvider;
use App\DataFixtures\Bluemmb\Faker;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\String\Slugger\SluggerInterface;

class AppFixtures extends Fixture
{
    protected $slugger;

    public function __construct(sluggerInterface $slugger)
    {
        $this->slugger = $slugger;
    }

    public function load(ObjectManager $manager): void
    {

        $faker = Factory::create('fr_FR');
        $faker->addProvider(new \Liior\Faker\Prices($faker));
        $faker->addProvider(new \Bezhanov\Faker\Provider\Commerce($faker));
        $faker->addProvider(new \Bluemmb\Faker\PicsumPhotosProvider($faker));


        for($c =0;$c < 3;$c++) {
            $category = new Category;
            $category->setName($faker->department)
                     ->setSlug(strtolower($this->slugger->slug($category->getName())));
            $manager->persist($category);

        for($p = 0; $p < mt_rand(15,20);$p++) {
            $product = new Product;
            $product->setName($faker->productName)
                    ->setPrice($faker->price(40,200))
                    ->setSlug(strtolower($this->slugger->slug($product->getName())))
                    ->setCategory($category)
                    ->setShortDescription($faker->paragraph())
                    ->setPicture($faker->imageUrl(400,400, true));
            
            $manager->persist($product);
            }
        }

        

        $manager->flush();
    }
}
