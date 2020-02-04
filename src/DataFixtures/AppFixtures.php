<?php

namespace App\DataFixtures;

use App\Entity\Ad;
use Faker\Factory;
use App\Entity\User;
use App\Entity\Image;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {
        // $product = new Product();
        // $manager->persist($product);
        $faker = Factory::create("Fr-fr");
        
        // gestion des utilisateurs
        $users = [];
        for($i = 0; $i < 10; $i++)
        {
            $user = new User();
            $genres = ['male', 'female'];

            // choix aléatoire du genre humain avec faker
            $genre = $faker->randomElement($genres);

            $picture = 'https://randomuser.me/api/portraits/';

            // génération de nombre aléatoire avec faker
            $pictureId = $faker->numberBetween(1,99).'.jpg';

            $hash = $this->encoder->encodePassword($user, 'password');

            // si $genre = male on forme l'url avec men pour obtenir l'avatar d'un male sinon se sera women
            $picture .= ($genre == 'male' ? 'men/':'women/').$pictureId;

            $user->setFirstName($faker->firstname($genre))
                 ->setLastName($faker->lastname)
                 ->setEmail($faker->email)
                 ->setIntroduction($faker->sentence())
                 ->setDescription("<p>".join("</p><p>", $faker->paragraphs(3))."</p>")
                 ->setHash($hash)
                 ->setPicture($picture);

            $manager->persist($user);
            $users[] = $user;
        }

        // Gestion des annonces
        for($i=0; $i < 12; $i++)
        {
            $ad = new Ad();

            // creation aleatoire d'un titre (une phrase)
            $titre = $faker->sentence();
            // creation aleatoire d'une introduction (un paragraphe)
            $introduction = $faker->paragraph(2);
            // creation aleatoire d'un contenu (plusieurs paragraphes)
            $contenu = "<p>".join("</p><p>", $faker->paragraphs(5))."</p>";
            // creation aleatoire d'images
            $image = $faker->imageUrl(1000,350);

            $user = $users[mt_rand(0, count($users) - 1)];
            
            // la calcul automatique du slug a été effectué dans la classe entity "Ad.php"

            $ad->setTitle($titre)
                ->setPrice(mt_rand(10, 100))
                ->setIntroduction($introduction)
                ->setContent($contenu)
                ->setCoverImage($image)
                ->setRooms(mt_rand(1,5))
                ->setAuthor($user);
            
            for($j=1; $j<=mt_rand(2,5); $j++)
            {
                $image = new Image();
                // creation aleatoire d'images
                $image->setUrl($faker->imageUrl(640,480))
                        ->setCaption($faker->sentence())
                        ->setRelation($ad);
                $manager->persist($image);

            }

            $manager->persist($ad);
        }

        $manager->flush();
    }
}
