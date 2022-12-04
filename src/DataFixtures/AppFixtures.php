<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Product;
use Liior\Faker\Prices;
use App\Entity\Category;
use bluemmb\Faker\PicsumPhotosProvider;
use App\DataFixtures\Bluemmb\Faker;
use App\Entity\Purchase;
use App\Entity\PurchaseItem;
use App\Entity\User;
use DateTimeImmutable;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

class AppFixtures extends Fixture
{
    protected $slugger;
    protected $encoder;

    public function __construct(sluggerInterface $slugger,UserPasswordEncoderInterface $encoder)
    {
        $this->slugger = $slugger;
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager): void
    {

        $faker = Factory::create('fr_FR');
        $faker->addProvider(new \Liior\Faker\Prices($faker));
        $faker->addProvider(new \Bezhanov\Faker\Provider\Commerce($faker));
        $faker->addProvider(new \Bluemmb\Faker\PicsumPhotosProvider($faker));

        $admin = new User;

        $hash = $this->encoder->encodePassword($admin, "password");

        $admin->setEmail("admin@gmail.com")
              ->setPassword($hash)
              ->setFullName("Admin")
              ->setRoles(["ROLE_ADMIN"]);
              
        $manager->persist($admin);

        $users = [];
        
        for($u =0; $u < 5 ;$u++) {
            $user = new User;

            $hash = $this->encoder->encodePassword($user, "password");

            $user->setEmail("user$u@gmail.com")
                 ->setFullName($faker->name())
                 ->setPassword($hash);

            $users[] = $user;

            $manager->persist($user);
        }
        //ici ,on se créer un tableau de products
        $products = [];


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

                //à chaque nouveau product que l'on va persister, on va le rajouter à notre 
                //tableau de produtcs

                $products[] = $product;
            
            $manager->persist($product);
            }
        }

        for($p = 0; $p < mt_rand(20,40); $p++) {
            $purchase = new Purchase;

            $purchase->setFullName($faker->name)
                     ->setAddress($faker->streetAddress)
                     ->setPostalCode($faker->postcode)
                     ->setCity($faker->city)
                     ->setUser($faker->randomElement($users))
                     ->setTotal(mt_rand(20,300))
                     ->setPurchasedAt(new DateTimeImmutable('- 6 months'));

            //ici je vais chercher un ensemble de produits au hasard que je vais appeler $selectedPurchases
            //donc dans $selectProducts, j'ai créer au hasard entre 3 et 5 produits de tous les produits crées au dessus
            $selectedPurchases = $faker->randomElements($products,mt_rand(3,5));


            //pour chacun de mes selectedProducts que je vais appeler product,je vais tt simplement dire  
            //$purchase->addProduct(et je lui donne le product en question $product)
            foreach($selectedPurchases as $product) {
               $purchaseItem = new PurchaseItem;
               $purchaseItem->setProduct($product)
                            ->setQuantity(mt_rand(1,3))
                            ->setProductName($product->getName())
                            ->setProductPrice($product->getPrice())
                            ->setTotal($purchaseItem->getProductPrice()* $purchaseItem->getQuantity()
                            )
                            ->setPurchase($purchase);

                $manager->persist($purchaseItem);
            }

            if($faker->boolean(90)) {
                $purchase->setStatus(Purchase::STATUS_PAID);
            }

            $manager->persist($purchase);
        }

        $manager->flush();
    }
}
