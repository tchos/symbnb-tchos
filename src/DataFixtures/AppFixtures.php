<?php

namespace App\DataFixtures;

use App\Entity\Ad;
use Faker\Factory;
use App\Entity\Image;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        // $product = new Product();
        // $manager->persist($product);
        
        for($i=0; $i < 12; $i++)
        {
            $faker = Factory::create("Fr-fr");
            $ad = new Ad();

            // creation aleatoire d'un titre (une phrase)
            $titre = $faker->sentence();
            // creation aleatoire d'une introduction (un paragraphe)
            $introduction = $faker->paragraph(2);
            // creation aleatoire d'un contenu (plusieurs paragraphes)
            $contenu = "<p>".join("</p><p>", $faker->paragraphs(5))."</p>";
            // creation aleatoire d'images
            $image = $faker->imageUrl(1000,350);
            
            // la calcul automatique du slug a été effectué dans la classe entity "Ad.php"

            $ad->setTitle($titre)
                ->setPrice(mt_rand(10, 100))
                ->setIntroduction($introduction)
                ->setContent($contenu)
                ->setCoverImage($image)
                ->setRooms(mt_rand(1,5));
            
            for($j=1; $j<=mt_rand(2,5); $j++)
            {
                $image = new Image();
                // creation aleatoire d'images
                $image->setUrl($faker->imageUrl())
                        ->setCaption($faker->sentence())
                        ->setRelation($ad);
                $manager->persist($image);

            }

            $manager->persist($ad);
        }

        $manager->flush();
    }
}
