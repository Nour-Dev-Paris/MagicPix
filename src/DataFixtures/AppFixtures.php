<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\User;
use App\Entity\Image;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder){
       $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {
        $faker = Factory::create('fr-FR');

        // Gestion des Users
        $users = [];
        $genres = ['male', 'female'];
        
        for ($i = 1; $i <= 25; $i++) {
            $user = new User();

            $genre = $faker->randomElement($genres);

            $picture = 'https://randomuser.me/api/portraits/';
            $pictureId = $faker->numberBetween(1, 99) . '.jpg'; 

            $picture .= ($genre == 'male' ? 'men/' : 'women/') . $pictureId;

            $hash = $this->encoder->encodePassword($user, 'password');

            $user->setPseudo($faker->firstname($genre))
                 ->setEmail($faker->email)
                 ->setIntroduction($faker->sentence())
                 ->setCoverImage($faker->imageUrl(1000,350))
                 ->setDescription('<p>' . join ('</p><p>' , $faker-> paragraphs(3)) . '</p>')
                 ->setHash($hash)
                 ->setAvatar($picture);

            for($j = 1; $j <= mt_rand(1, 15); $j++) {
                $image = new Image();

                $image->setUrl($faker->imageUrl())
                      ->setCaption($faker->sentence())
                      ->setUser($user);

                $manager->persist($image);
            }

            $manager->persist($user);
            $users[] = $user;
        }
        
        // $product = new Product();

        $manager->flush();
    }
}
